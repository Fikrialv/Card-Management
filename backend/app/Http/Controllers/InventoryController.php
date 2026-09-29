<?php

namespace App\Http\Controllers;

use App\Actions\AssignRfid;
use App\Http\Requests\AssignRfidRequest;
use App\Models\CustomerRequest;
use App\Models\RfidCard;
use App\Models\RfidRange;
use App\Models\StockMovement;
use App\Models\SystemSetting;
use App\Services\LowStockNotifier;
use App\Support\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class InventoryController extends Controller
{
    public function index(Request $request, LowStockNotifier $lowStockNotifier): Response
    {
        $period = $request->string('period', 'all')->toString();
        $from = match ($period) {
            'today' => today(), 'yesterday' => today()->subDay(), 'week' => today()->startOfWeek(), 'month' => today()->startOfMonth(), 'custom' => $request->date('from'), default => null
        };
        $to = $period === 'yesterday' ? today()->subDay() : ($period === 'custom' ? $request->date('to') : null);
        $movement = fn () => StockMovement::query()->when($from, fn ($q) => $q->whereDate('occurred_on', '>=', $from))->when($to, fn ($q) => $q->whereDate('occurred_on', '<=', $to));

        return Inertia::render('Inventory/Index', [
            'summary' => [
                'available' => RfidCard::query()->where('status', 'available')->count(),
                'assigned' => RfidCard::query()->where('status', 'assigned')->count(),
                'stock_in' => $movement()->where('type', 'IN')->sum('quantity'),
                'stock_out' => $movement()->where('type', 'OUT')->sum('quantity'),
                'low_stock_threshold' => $lowStockNotifier->threshold(),
            ],
            'ranges' => RfidRange::query()->when($from, fn ($q) => $q->whereDate('occurred_on', '>=', $from))->when($to, fn ($q) => $q->whereDate('occurred_on', '<=', $to))->latest()->paginate(25)->withQueryString(),
            'filters' => ['period' => $period, 'from' => $request->input('from'), 'to' => $request->input('to')],
        ]);
    }

    public function updateLowStockThreshold(Request $request): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);
        $data = $request->validate(['threshold' => ['required', 'integer', 'min:0', 'max:1000000']]);
        $setting = SystemSetting::query()->updateOrCreate(
            ['key' => 'inventory.low_stock_threshold'],
            ['value' => ['threshold' => $data['threshold']], 'updated_by' => $request->user()->id],
        );
        Audit::record('settings.low_stock_threshold_updated', $setting, $request->user()->id, ['threshold' => $data['threshold']]);

        return $request->header('X-Inertia')
            ? redirect()->back(303)
            : response()->json(['data' => ['threshold' => $data['threshold']]]);
    }

    public function assign(AssignRfidRequest $request, CustomerRequest $customerRequest, AssignRfid $action): JsonResponse|RedirectResponse
    {
        $updated = $action->handle($customerRequest, $request->validated('rfid_number'), $request->user());

        return $request->header('X-Inertia')
            ? redirect()->back(303)
            : response()->json(['data' => ['id' => $updated->id, 'status' => $updated->status]]);
    }
}
