<?php

namespace Tests\Feature;

use App\Models\CustomerRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class KartuBaruQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_card_types_accept_quantity_without_payment_proof(): void
    {
        Storage::fake('local');
        $this->getJson('/api/public/v1/request-requirements?requested_quantity=10')->assertOk()->assertJsonPath('data.payment_required', false)->assertJsonMissingPath('data.remaining');
        $this->post('/api/public/v1/requests', [
            'institution_name' => 'PT Maju', 'crm_number' => 'CRMFREE01', 'request_type' => 'new_card', 'requested_quantity' => 10, 'request_date' => '2026-09-14',
            'application_file' => UploadedFile::fake()->createWithContent('template.xlsx', 'PK\x03\x04xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ])->assertCreated();
        $this->assertDatabaseHas('customer_requests', ['requested_quantity' => 10]);
    }

    public function test_approval_does_not_create_or_consume_legacy_quota(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $request = CustomerRequest::factory()->create(['status' => 'UNDER_REVIEW', 'requested_quantity' => 300]);
        $this->actingAs($admin)->postJson("/requests/{$request->id}/approve")->assertOk()->assertJsonPath('data.status', 'APPROVED');
        $this->assertDatabaseMissing('customer_requests', ['id' => $request->id, 'requested_quantity' => 30]);
    }
}
