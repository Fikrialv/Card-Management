<?php

namespace Tests\Feature;

use App\Models\CustomerRequest;
use App\Models\ReportExport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_viewer_can_view_and_export_filtered_reports(): void
    {
        CustomerRequest::factory()->create(['request_date' => '2026-09-01', 'status' => 'NEW']);
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer']);
        $this->actingAs($viewer)->get('/reports')->assertOk();
        $this->actingAs($admin)->get('/reports?date_from=2026-09-01&date_to=2026-09-01')->assertOk()->assertInertia(fn ($page) => $page->component('Reports/Index')->where('summary.total_requests', 1));
        $this->actingAs($admin)->get('/reports/export/excel?date_from=2026-09-01&date_to=2026-09-01')->assertOk()->assertHeader('Content-Type', 'application/vnd.ms-excel')->assertSee('Nomor pengajuan');
        $export = ReportExport::query()->latest('id')->firstOrFail();
        $this->actingAs($admin)->get('/reports/export-file/'.$export->id)->assertOk();
        $this->actingAs($viewer)->get('/reports/export/pdf')->assertOk();
        $this->actingAs($viewer)->get('/reports/export-file/'.$export->id)->assertOk();
    }

    public function test_excel_export_neutralizes_formula_like_values(): void
    {
        $record = CustomerRequest::factory()->create(['request_date' => '2026-09-01']);
        $record->customer->update(['institution_name' => '=SUM(A1:A2)']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/reports/export/excel');

        $response->assertOk();
        $this->assertStringContainsString("'=SUM(A1:A2)", $response->getContent());
    }
}
