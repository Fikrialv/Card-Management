<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\CustomerRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProcurementNoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Public/RequestPortal'))->name('public.request');
Route::redirect('/pengajuan', '/');
Route::get('/tracking', fn () => Inertia::render('Public/RequestPortal', ['trackingOnly' => true]))->name('public.tracking');

Route::middleware('auth')->group(function (): void {
    Route::get('/admin', function (Request $request) {
        abort_unless($request->user()->role === 'admin', 403);

        return redirect()->route('dashboard');
    })->name('admin.home');
    Route::get('/viewer', function (Request $request) {
        abort_unless($request->user()->role === 'viewer', 403);

        return redirect()->route('dashboard');
    })->name('viewer.home');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    Route::post('/notifications/{userNotification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/requests', [CustomerRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{customerRequest}', [CustomerRequestController::class, 'show'])->name('requests.show');
    Route::get('/request-evidence/{requestEvidence}', [CustomerRequestController::class, 'downloadEvidence'])->name('request-evidence.download');
    Route::post('/requests/{customerRequest}/start-review', [CustomerRequestController::class, 'startReview'])->name('requests.start-review');
    Route::post('/requests/{customerRequest}/approve', [CustomerRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{customerRequest}/reject', [CustomerRequestController::class, 'reject'])->name('requests.reject');
    Route::post('/requests/{customerRequest}/prioritize', [CustomerRequestController::class, 'prioritize'])->name('requests.prioritize');
    Route::post('/requests/{customerRequest}/complete', [CustomerRequestController::class, 'complete'])->name('requests.complete');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::put('/inventory/low-stock-threshold', [InventoryController::class, 'updateLowStockThreshold'])->name('inventory.low-stock-threshold');
    Route::post('/requests/{customerRequest}/assign', [InventoryController::class, 'assign'])->name('inventory.assign');
    Route::get('/procurement-notes', [ProcurementNoteController::class, 'index'])->name('procurement-notes.index');
    Route::post('/procurement-notes', [ProcurementNoteController::class, 'store'])->name('procurement-notes.store');
    Route::post('/procurement-notes/{procurementNote}/receive', [ProcurementNoteController::class, 'receive'])->name('procurement-notes.receive');
    Route::delete('/procurement-notes/{procurementNote}', [ProcurementNoteController::class, 'destroy'])->name('procurement-notes.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/export-file/{reportExport}', [ReportController::class, 'download'])->name('reports.download');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/locale', [ProfileController::class, 'locale'])->name('locale.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
