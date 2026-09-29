<?php

namespace Tests\Feature;

use App\Actions\StockIn;
use App\Models\CustomerRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class InventoryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_level_assignment_moves_one_card_and_notifies_processing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        app(StockIn::class)->handle(['start_number' => '10001', 'end_number' => '10002', 'source' => 'Test receipt', 'occurred_on' => today()->toDateString()], $admin);
        $request = CustomerRequest::factory()->create(['status' => 'APPROVED']);

        $this->actingAs($admin)->postJson("/requests/{$request->id}/assign", ['rfid_number' => '10001'])->assertOk()->assertJsonPath('data.status', 'PROCESSING');
        $this->assertDatabaseHas('rfid_cards', ['number' => '10001', 'status' => 'assigned']);
        $this->assertDatabaseHas('stock_movements', ['type' => 'OUT', 'quantity' => 1, 'request_item_id' => null]);
        $this->assertDatabaseHas('rfid_assignments', ['customer_request_id' => $request->id, 'request_item_id' => null]);
        $this->assertDatabaseHas('customer_notifications', ['customer_request_id' => $request->id, 'type' => 'request.status_changed']);
        $this->actingAs($admin)->postJson("/requests/{$request->id}/assign", ['rfid_number' => '10002'])->assertUnprocessable();
    }

    public function test_completion_requires_request_level_assignment_not_request_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $request = CustomerRequest::factory()->create(['status' => 'PROCESSING']);
        $this->actingAs($admin)->postJson("/requests/{$request->id}/complete")->assertUnprocessable();
        app(StockIn::class)->handle(['start_number' => '40001', 'end_number' => '40001', 'source' => 'Completion test', 'occurred_on' => today()->toDateString()], $admin);
        $this->actingAs($admin)->postJson("/requests/{$request->id}/assign", ['rfid_number' => '40001'])->assertOk();
        $this->actingAs($admin)->postJson("/requests/{$request->id}/complete")->assertOk()->assertJsonPath('data.status', 'COMPLETED');
    }

    public function test_overlapping_stock_range_is_rejected_without_partial_rows(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = ['start_number' => '20001', 'end_number' => '20003', 'source' => 'Test receipt', 'occurred_on' => today()->toDateString()];
        app(StockIn::class)->handle($payload, $admin);
        $this->expectException(ValidationException::class);
        app(StockIn::class)->handle([...$payload, 'start_number' => '20003', 'end_number' => '20005'], $admin);
        $this->assertDatabaseCount('rfid_cards', 3);
    }

    public function test_low_stock_notifies_authorized_operators_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer']);
        $this->actingAs($admin)->putJson('/inventory/low-stock-threshold', ['threshold' => 2])->assertOk();
        app(StockIn::class)->handle(['start_number' => '60001', 'end_number' => '60001', 'source' => 'Low stock', 'occurred_on' => today()->toDateString()], $admin);
        $request = CustomerRequest::factory()->create(['status' => 'APPROVED']);
        $this->actingAs($admin)->postJson("/requests/{$request->id}/assign", ['rfid_number' => '60001'])->assertOk();
        $this->assertDatabaseHas('user_notifications', ['user_id' => $admin->id, 'type' => 'inventory.low_stock']);
        $this->assertDatabaseMissing('user_notifications', ['user_id' => $viewer->id, 'type' => 'inventory.low_stock']);
    }
}
