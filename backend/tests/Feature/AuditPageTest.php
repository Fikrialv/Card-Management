<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_audit_activity_page(): void
    {
        $user = User::factory()->create(['role' => 'viewer']);
        AuditLog::create(['actor_id' => $user->id, 'action' => 'request.approved', 'auditable_type' => 'App\\Models\\CustomerRequest', 'auditable_id' => 1, 'correlation_id' => 'test-correlation']);

        $this->actingAs($user)
            ->get('/audit')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Audit/Index')->where('events.data.0.action', 'request.approved')->where('events.data.0.actor.name', $user->name));
    }
}
