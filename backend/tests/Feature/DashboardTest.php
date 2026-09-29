<?php

namespace Tests\Feature;

use App\Models\CustomerRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_the_monitoring_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'viewer']);
        CustomerRequest::factory()->create(['request_date' => today()->subDay(), 'status' => 'NEW']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Dashboard')->where('metrics.2.value', 1)->has('queue.data', 1));
    }
}
