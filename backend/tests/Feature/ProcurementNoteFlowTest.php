<?php

namespace Tests\Feature;

use App\Models\ProcurementNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProcurementNoteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_receive_a_procurement_note_into_stock_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $created = $this->actingAs($admin)->postJson('/procurement-notes', [
            'request_date' => '2026-09-14',
            'pic_name' => 'Rani Pratama',
            'requested_quantity' => 3,
            'notes' => 'Pengadaan awal demonstrasi',
        ])->assertCreated();

        $note = ProcurementNote::query()->findOrFail($created->json('data.id'));
        $receipt = [
            'received_on' => '2026-09-15',
            'received_quantity' => 3,
        ];

        $this->actingAs($admin)->postJson("/procurement-notes/{$note->id}/receive", $receipt)
            ->assertOk()
            ->assertJsonPath('data.status', 'RECEIVED');

        $this->assertDatabaseHas('procurement_notes', ['id' => $note->id, 'received_quantity' => 3, 'status' => 'RECEIVED']);
        $this->assertDatabaseCount('rfid_cards', 3);
        $this->assertDatabaseHas('stock_movements', ['procurement_note_id' => $note->id, 'type' => 'IN', 'quantity' => 3]);

        $this->actingAs($admin)->postJson("/procurement-notes/{$note->id}/receive", $receipt)->assertUnprocessable();
        $this->assertDatabaseCount('rfid_cards', 3);
    }

    public function test_non_admin_cannot_create_or_receive_procurement_notes(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);
        $this->actingAs($viewer)->postJson('/procurement-notes', [
            'request_date' => '2026-09-14', 'pic_name' => 'Rani Pratama', 'requested_quantity' => 3,
        ])->assertForbidden();
    }

    public function test_admin_can_receive_procurement_in_partial_quantities_without_rfid_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $note = ProcurementNote::create([
            'reference_number' => 'PROC-PARTIAL', 'request_date' => '2026-09-14', 'pic_name' => 'Rani',
            'requested_quantity' => 300, 'status' => 'DRAFT', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->postJson("/procurement-notes/{$note->id}/receive", ['received_on' => '2026-09-15', 'received_quantity' => 250])->assertOk()->assertJsonPath('data.status', 'PARTIAL');
        $this->actingAs($admin)->postJson("/procurement-notes/{$note->id}/receive", ['received_on' => '2026-09-16', 'received_quantity' => 50])->assertOk()->assertJsonPath('data.status', 'RECEIVED');
        $this->assertDatabaseHas('procurement_notes', ['id' => $note->id, 'received_quantity' => 300, 'status' => 'RECEIVED']);
        $this->assertDatabaseCount('rfid_cards', 300);
        $this->assertDatabaseCount('stock_movements', 2);
    }

    public function test_partial_receipt_rejects_zero_negative_and_over_receipt(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $note = ProcurementNote::create([
            'reference_number' => 'PROC-EDGE', 'request_date' => '2026-09-14', 'pic_name' => 'Rani',
            'requested_quantity' => 10, 'status' => 'DRAFT', 'created_by' => $admin->id,
        ]);

        foreach ([0, -1, 11] as $quantity) {
            $this->actingAs($admin)->postJson("/procurement-notes/{$note->id}/receive", [
                'received_on' => '2026-09-15', 'received_quantity' => $quantity,
            ])->assertUnprocessable();
        }
    }

    public function test_admin_can_delete_only_unreceived_draft_notes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer']);
        $draft = ProcurementNote::create([
            'reference_number' => 'PROC-DELETE', 'request_date' => '2026-09-14', 'pic_name' => 'Rani',
            'requested_quantity' => 10, 'status' => 'DRAFT', 'created_by' => $admin->id,
        ]);

        $this->actingAs($viewer)->deleteJson("/procurement-notes/{$draft->id}")->assertForbidden();
        $this->actingAs($admin)->deleteJson("/procurement-notes/{$draft->id}")->assertOk()->assertJson(['deleted' => true]);
        $this->assertDatabaseMissing('procurement_notes', ['id' => $draft->id]);
    }

    public function test_received_procurement_notes_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $note = ProcurementNote::create([
            'reference_number' => 'PROC-LOCKED', 'request_date' => '2026-09-14', 'pic_name' => 'Rani',
            'requested_quantity' => 10, 'received_quantity' => 10, 'status' => 'RECEIVED', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->deleteJson("/procurement-notes/{$note->id}")->assertStatus(422);
        $this->assertDatabaseHas('procurement_notes', ['id' => $note->id]);
    }
}
