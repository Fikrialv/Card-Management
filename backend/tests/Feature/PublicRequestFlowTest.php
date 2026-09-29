<?php

namespace Tests\Feature;

use App\Models\RequestEvidence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class PublicRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_download_exposes_immutable_version_and_hash(): void
    {
        $response = $this->get('/api/public/v1/request-template');

        $response->assertOk()
            ->assertHeader('X-Template-Version', '2025')
            ->assertHeader('ETag', 'bc980355e78422889f535121778c8311168d1dec6b617504fce4be4b8021775b');
    }

    public function test_customer_can_submit_the_required_excel_application_and_payment_proof_then_track_safely(): void
    {
        Storage::fake('local');

        $submission = $this->post('/api/public/v1/requests', [
            'institution_name' => 'Instansi Uji',
            'crm_number' => 'CRMTEST001',
            'request_type' => 'system_error_card',
            'requested_quantity' => 2,
            'request_date' => '2026-09-14',
            'application_file' => UploadedFile::fake()->createWithContent('pengajuan-rfid.xlsx', 'PK\x03\x04xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'payment_proof' => UploadedFile::fake()->createWithContent('bukti-bayar.pdf', '%PDF-1.4 test', 'application/pdf'),
        ]);

        $submission->assertCreated()
            ->assertJsonPath('data.status', 'NEW')
            ->assertJsonStructure(['data' => ['request_number', 'tracking_code', 'status']]);

        $this->assertDatabaseHas('customer_requests', [
            'request_type' => 'system_error_card',
            'request_date' => '2026-09-14 00:00:00',
            'deadline' => null,
        ]);
        $this->assertDatabaseCount('request_items', 0);
        $this->assertDatabaseHas('request_evidence', ['artifact_type' => 'application']);
        $this->assertDatabaseHas('request_evidence', ['artifact_type' => 'payment_proof']);

        $tracking = $this->postJson('/api/public/v1/tracking', [
            'request_number' => $submission->json('data.request_number'),
            'tracking_code' => $submission->json('data.tracking_code'),
        ]);

        $tracking->assertOk()
            ->assertJsonPath('data.status', 'NEW')
            ->assertJsonMissingPath('data.tracking_code_hash')
            ->assertJsonMissingPath('data.internal_notes');
    }

    public function test_customer_request_rejects_removed_fields_invalid_crm_and_missing_required_uploads(): void
    {
        $this->postJson('/api/public/v1/requests', [
            'institution_name' => 'Instansi Uji',
            'crm_number' => 'CRM-TOO-LONG-01',
            'request_type' => 'activation',
            'requested_quantity' => 1,
            'request_date' => '2026-09-14',
            'city' => 'Jakarta',
            'items' => [['license_plate' => 'B 1001 TEST']],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['crm_number', 'request_type', 'application_file']);
    }

    public function test_disguised_upload_is_rejected_by_signature_validation(): void
    {
        Storage::fake('local');
        $this->postJson('/api/public/v1/requests', [
            'institution_name' => 'Instansi Signature',
            'crm_number' => 'CRMSIGNATURE',
            'request_type' => 'new_card',
            'requested_quantity' => 1,
            'request_date' => '2026-09-14',
            'application_file' => UploadedFile::fake()->createWithContent('pengajuan.xlsx', 'not-an-xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'payment_proof' => UploadedFile::fake()->image('bukti.png'),
        ])->assertUnprocessable()->assertJsonPath('errors.application_file.0', 'validation.invalid_file_signature');
        $this->assertDatabaseCount('customer_requests', 0);
    }

    public function test_quantity_zero_negative_and_oversized_upload_are_rejected(): void
    {
        Storage::fake('local');
        $base = [
            'institution_name' => 'Instansi Validasi',
            'crm_number' => 'CRMVALIDASI',
            'request_type' => 'new_card',
            'request_date' => '2026-09-14',
        ];

        foreach ([0, -1] as $quantity) {
            $this->postJson('/api/public/v1/requests', $base + [
                'requested_quantity' => $quantity,
                'application_file' => UploadedFile::fake()->createWithContent('pengajuan.xlsx', 'PK\x03\x04xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ])->assertUnprocessable()->assertJsonValidationErrors(['requested_quantity']);
        }

        $this->postJson('/api/public/v1/requests', $base + [
            'requested_quantity' => 1,
            'application_file' => UploadedFile::fake()->create('pengajuan.xlsx', 3073, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ])->assertUnprocessable()->assertJsonValidationErrors(['application_file']);
    }

    public function test_submission_retry_is_idempotent(): void
    {
        Storage::fake('local');
        $payload = [
            'institution_name' => 'Instansi Idempoten',
            'crm_number' => 'CRMIDEMPOTENT',
            'request_type' => 'lost_card',
            'requested_quantity' => 1,
            'request_date' => '2026-09-14',
            'application_file' => UploadedFile::fake()->createWithContent('pengajuan.xlsx', 'PK\x03\x04xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'payment_proof' => UploadedFile::fake()->image('bukti.png'),
        ];
        $headers = ['Idempotency-Key' => 'request_retry_key_123456789'];

        $first = $this->withHeaders($headers)->post('/api/public/v1/requests', $payload)->assertCreated();
        $second = $this->withHeaders($headers)->post('/api/public/v1/requests', $payload)->assertCreated();

        $this->assertSame($first->json('data.request_number'), $second->json('data.request_number'));
        $this->assertSame($first->json('data.tracking_code'), $second->json('data.tracking_code'));
        $this->assertDatabaseCount('customer_requests', 1);
    }

    public function test_evidence_is_private_and_requires_internal_authentication(): void
    {
        Storage::fake('local');
        $this->post('/api/public/v1/requests', [
            'institution_name' => 'Instansi Bukti',
            'crm_number' => 'CRMEVIDENCE',
            'request_type' => 'damaged_card',
            'requested_quantity' => 1,
            'request_date' => '2026-09-14',
            'application_file' => UploadedFile::fake()->createWithContent('pengajuan.xlsx', 'PK\x03\x04xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'payment_proof' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertCreated();

        $evidence = RequestEvidence::query()->firstOrFail();
        Storage::disk('local')->assertExists($evidence->path);
        $this->get("/request-evidence/{$evidence->id}")->assertRedirect('/login');
        $this->actingAs(User::factory()->create(['role' => 'viewer']))
            ->get("/request-evidence/{$evidence->id}")->assertForbidden();
    }

    public function test_tracking_uses_generic_not_found_response(): void
    {
        $this->postJson('/api/public/v1/tracking', [
            'request_number' => 'REQ-UNKNOWN',
            'tracking_code' => 'wrong-code',
        ])->assertNotFound()
            ->assertJsonPath('message', 'tracking.not_found');
    }

    public function test_public_api_is_rate_limited_per_ip(): void
    {
        $request = fn () => $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.42'])
            ->postJson('/api/public/v1/tracking', ['request_number' => 'REQ-RATE', 'tracking_code' => 'invalid']);

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $request()->assertNotFound();
        }

        $request()->assertStatus(429);
    }
}
