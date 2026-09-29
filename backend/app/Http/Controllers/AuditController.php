<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class AuditController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->canReviewRequests(), 403);
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'in:request,inventory,procurement,report,settings'], 'action' => ['nullable', 'string', 'max:100']]);
        $events = AuditLog::query()->with('actor:id,name')->when($filters['category'] ?? null, fn ($query, string $value) => $query->where(function ($query) use ($value): void {
            $prefixes = ['request' => ['request.%'], 'inventory' => ['inventory.%'], 'procurement' => ['procurement.%', 'procurement_note.%'], 'report' => ['report.%'], 'settings' => ['settings.%']][$value];
            foreach ($prefixes as $prefix) {
                $query->orWhere('action', 'like', $prefix);
            }
        }))->when($filters['search'] ?? null, fn ($query, string $value) => $query->where(function ($query) use ($value): void {
            $query->where('action', 'like', '%'.$value.'%')->orWhere('auditable_id', $value);
        }))->when($filters['action'] ?? null, fn ($query, string $value) => $query->where('action', $value))->latest()->paginate(25)->withQueryString();

        return Inertia::render('Audit/Index', ['filters' => $filters, 'events' => $events]);
    }
}
