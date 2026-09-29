<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_root_renders_the_public_customer_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Public/RequestPortal'))
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Correlation-ID');

        $this->get('/pengajuan')->assertRedirect('/');
    }

    public function test_guest_cannot_open_internal_admin_entrypoint(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_role_entrypoints_are_restricted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($admin)->get('/admin')->assertRedirect('/dashboard');
        $this->actingAs($admin)->get('/viewer')->assertForbidden();
        $this->actingAs($viewer)->get('/viewer')->assertRedirect('/dashboard');
        $this->actingAs($viewer)->get('/admin')->assertForbidden();
    }
}
