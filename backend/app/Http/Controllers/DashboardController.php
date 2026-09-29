<?php

namespace App\Http\Controllers;

use App\Http\Resources\InternalCustomerRequestResource;
use App\Models\AuditLog;
use App\Models\CustomerRequest;
use App\Models\RfidCard;
use App\Models\StockMovement;
use App\Models\UserNotification;
use App\Services\PriorityQueue;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(Request $request, PriorityQueue $queue): Response
    {
        $today = today();
        $open = CustomerRequest::query()->whereNotIn('status', ['COMPLETED', 'REJECTED']);
        $due = (clone $open)->whereDate('request_date', $today->copy()->subDay());
        $late = (clone $open)->whereDate('request_date', '<', $today->copy()->subDay());

        return Inertia::render('Dashboard', [
            'metrics' => [
                ['label' => 'Stok siap pakai', 'value' => RfidCard::query()->where('status', 'available')->count(), 'detail' => 'kartu RFID tersedia', 'tone' => 'blue'],
                ['label' => 'Menunggu review', 'value' => CustomerRequest::query()->whereIn('status', ['NEW', 'UNDER_REVIEW'])->count(), 'detail' => 'perlu tindakan PIC', 'tone' => 'amber'],
                ['label' => 'Perlu ditindaklanjuti', 'value' => $due->count() + $late->count(), 'detail' => 'harus selesai hari ini atau terlambat', 'tone' => 'rose'],
                ['label' => 'Selesai hari ini', 'value' => CustomerRequest::query()->where('status', 'COMPLETED')->whereDate('updated_at', $today)->count(), 'detail' => 'proses telah tuntas', 'tone' => 'emerald'],
            ],
            'queue' => InternalCustomerRequestResource::collection($queue->ordered([])->limit(5)->get()),
            'activity' => AuditLog::query()->latest()->limit(5)->get(['id', 'action', 'auditable_type', 'auditable_id', 'created_at']),
            'unread_notifications' => UserNotification::query()->where('user_id', $request->user()->id)->whereNull('read_at')->count(),
            'stock' => ['in' => (int) StockMovement::query()->where('type', 'IN')->sum('quantity'), 'out' => (int) StockMovement::query()->where('type', 'OUT')->sum('quantity')],
        ]);
    }
}
