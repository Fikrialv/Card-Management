<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DocumentsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_documents_route_is_removed_from_active_mvp(): void
    {
        $user = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($user)->get('/documents')->assertNotFound();
    }
}
