<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_orders_utamakan_then_due_or_late_then_oldest_request_date(): void
    {
        $reviewer = User::factory()->create(['role' => 'viewer']);
        $customer = Customer::factory()->create();
        CustomerRequest::factory()->for($customer)->create(['request_number' => 'REQ-LATE', 'request_date' => today()->subDays(3)]);
        CustomerRequest::factory()->for($customer)->create(['request_number' => 'REQ-DUE', 'request_date' => today()->subDay()]);
        CustomerRequest::factory()->for($customer)->create(['request_number' => 'REQ-UTAMA', 'request_date' => today(), 'prioritized' => true]);
        $this->actingAs($reviewer)->get('/requests')->assertInertia(fn ($page) => $page
            ->where('requests.data.0.request_number', 'REQ-UTAMA')
            ->where('requests.data.1.request_number', 'REQ-LATE')
            ->where('requests.data.2.request_number', 'REQ-DUE'));
    }

    public function test_empty_prioritized_filter_does_not_hide_prioritized_requests(): void
    {
        CustomerRequest::factory()->create(['request_date' => today(), 'prioritized' => true]);
        CustomerRequest::factory()->create(['request_date' => today(), 'prioritized' => false]);

        $this->actingAs(User::factory()->create(['role' => 'viewer']))
            ->get('/requests?prioritized=')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('requests.data', 2));
    }

    public function test_target_h_plus_one_and_labels_are_exposed(): void
    {
        $reviewer = User::factory()->create(['role' => 'viewer']);
        CustomerRequest::factory()->create(['request_date' => today()->subDay()]);
        $this->actingAs($reviewer)->get('/requests')->assertInertia(fn ($page) => $page
            ->where('requests.data.0.target_state', 'DUE_TODAY')
            ->where('requests.data.0.target_date', today()->toDateString()));
    }

    public function test_utamakan_is_a_simple_authorized_control_and_never_approves(): void
    {
        $reviewer = User::factory()->create(['role' => 'admin']);
        $request = CustomerRequest::factory()->create(['status' => 'UNDER_REVIEW']);
        $this->actingAs($reviewer)->postJson("/requests/{$request->id}/prioritize", ['prioritized' => true, 'reason' => 'Operasional harus didahulukan'])->assertOk();
        $this->assertDatabaseHas('customer_requests', ['id' => $request->id, 'prioritized' => 1, 'status' => 'UNDER_REVIEW']);
    }

    public function test_viewer_cannot_change_utamakan_or_approve_request(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);
        $request = CustomerRequest::factory()->create(['status' => 'UNDER_REVIEW']);
        $this->actingAs($viewer)->postJson("/requests/{$request->id}/prioritize", ['prioritized' => true])->assertForbidden();
        $this->actingAs($viewer)->postJson("/requests/{$request->id}/approve")->assertForbidden();
    }

    public function test_queue_applies_combined_search_type_and_date_filters_with_pagination(): void
    {
        $reviewer = User::factory()->create(['role' => 'viewer']);
        $customer = Customer::factory()->create(['institution_name' => 'Northwind Fleet', 'crm_number' => 'CRM-42']);
        CustomerRequest::factory()->for($customer)->create([
            'request_number' => 'REQ-MATCH',
            'request_type' => 'damaged_card',
            'request_date' => today()->subDay(),
        ]);
        CustomerRequest::factory()->for($customer)->create([
            'request_number' => 'REQ-OLD',
            'request_type' => 'damaged_card',
            'request_date' => today()->subDays(10),
        ]);
        CustomerRequest::factory()->for($customer)->create([
            'request_number' => 'REQ-TYPE',
            'request_type' => 'new_card',
            'request_date' => today()->subDay(),
        ]);

        $this->actingAs($reviewer)
            ->get('/requests?search=CRM-42&request_type=damaged_card&date_from='.today()->subDays(2)->toDateString().'&date_to='.today()->toDateString())
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('requests.data', 1)
                ->where('requests.data.0.request_number', 'REQ-MATCH')
                ->where('requests.meta.current_page', 1));
    }
}
