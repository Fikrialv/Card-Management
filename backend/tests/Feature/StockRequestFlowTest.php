<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StockRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_request_routes_are_removed_from_active_mvp(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/stock-requests')->assertNotFound();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->postJson('/stock-requests', [])->assertNotFound();
    }
}
