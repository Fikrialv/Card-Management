<?php

namespace Tests\Feature;

use App\Models\CustomerNotification;
use App\Models\CustomerRequest;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class NotificationInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_and_late_command_notifies_authorized_operators_once_per_day(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer']);
        User::factory()->create(['role' => 'viewer']);
        $due = CustomerRequest::factory()->create(['request_date' => today()->subDay(), 'status' => 'NEW']);
        $late = CustomerRequest::factory()->create(['request_date' => today()->subDays(2), 'status' => 'UNDER_REVIEW']);

        $this->artisan('requests:notify-targets')->assertSuccessful();
        $this->artisan('requests:notify-targets')->assertSuccessful();

        $this->assertDatabaseHas('user_notifications', ['user_id' => $admin->id, 'dedupe_key' => 'request-target:'.$due->id.':'.today()->toDateString().':'.$admin->id]);
        $this->assertDatabaseHas('user_notifications', ['user_id' => $viewer->id, 'dedupe_key' => 'request-target:'.$due->id.':'.today()->toDateString().':'.$viewer->id]);
        $this->assertSame(6, UserNotification::query()->count());
    }

    public function test_tracking_returns_only_notifications_for_authenticated_request_and_customer_can_mark_one_read(): void
    {
        Storage::fake('local');
        $submission = $this->post('/api/public/v1/requests', [
            'institution_name' => 'Instansi Notifikasi',
            'crm_number' => 'CRMNOTIF001',
            'request_type' => 'new_card',
            'requested_quantity' => 1,
            'request_date' => '2026-09-14',
            'application_file' => UploadedFile::fake()->createWithContent('pengajuan.xlsx', 'PK\x03\x04xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'payment_proof' => UploadedFile::fake()->createWithContent('bukti.pdf', '%PDF-1.4 test', 'application/pdf'),
        ])->assertCreated();

        $requestNumber = $submission->json('data.request_number');
        $trackingCode = $submission->json('data.tracking_code');
        $notification = CustomerNotification::query()->firstOrFail();
        CustomerNotification::query()->create([
            'customer_request_id' => $notification->customer_request_id,
            'type' => 'request.status_changed',
            'data' => ['request_number' => $requestNumber, 'status' => 'APPROVED'],
        ]);

        $this->postJson('/api/public/v1/tracking', [
            'request_number' => $requestNumber,
            'tracking_code' => $trackingCode,
        ])->assertOk()
            ->assertJsonCount(2, 'data.notifications')
            ->assertJsonPath('data.notifications.0.read_at', null);

        $this->postJson('/api/public/v1/tracking/notifications/read', [
            'request_number' => $requestNumber,
            'tracking_code' => $trackingCode,
            'notification_id' => $notification->id,
        ])->assertNoContent();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_tracking_cannot_read_notification_from_another_request(): void
    {
        $first = CustomerRequest::factory()->create(['tracking_code_hash' => Hash::make('first-code')]);
        $second = CustomerRequest::factory()->create(['tracking_code_hash' => Hash::make('second-code')]);
        $notification = CustomerNotification::query()->create([
            'customer_request_id' => $second->id,
            'type' => 'request.received',
            'data' => ['request_number' => $second->request_number],
        ]);

        $this->postJson('/api/public/v1/tracking/notifications/read', [
            'request_number' => $first->request_number,
            'tracking_code' => 'first-code',
            'notification_id' => $notification->id,
        ])->assertNotFound()
            ->assertJsonPath('message', 'tracking.not_found');
    }

    public function test_notification_page_is_removed_but_internal_user_can_mark_only_own_notification_read(): void
    {
        $owner = User::factory()->create(['role' => 'viewer']);
        $other = User::factory()->create(['role' => 'viewer']);
        $owned = UserNotification::query()->create(['user_id' => $owner->id, 'type' => 'request.created', 'data' => ['request_number' => 'REQ-OWNED']]);
        UserNotification::query()->create(['user_id' => $other->id, 'type' => 'request.created', 'data' => ['request_number' => 'REQ-OTHER']]);

        $this->actingAs($owner)->get('/notifications')->assertNotFound();

        $this->actingAs($owner)->post("/notifications/{$owned->id}/read")
            ->assertRedirect('/dashboard');

        $this->assertNotNull($owned->fresh()->read_at);
    }

    public function test_status_transition_creates_customer_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $request = CustomerRequest::factory()->create(['status' => 'NEW']);

        $this->actingAs($admin)->postJson("/requests/{$request->id}/start-review")
            ->assertOk();

        $this->assertDatabaseHas('customer_notifications', [
            'customer_request_id' => $request->id,
            'type' => 'request.status_changed',
        ]);
    }
}
