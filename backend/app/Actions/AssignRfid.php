<?php

namespace App\Actions;

use App\Models\CustomerNotification;
use App\Models\CustomerRequest;
use App\Models\RequestStatusHistory;
use App\Models\RfidAssignment;
use App\Models\RfidCard;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\LowStockNotifier;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AssignRfid
{
    public function __construct(private readonly LowStockNotifier $lowStockNotifier) {}

    public function handle(CustomerRequest $request, string $number, User $actor): CustomerRequest
    {
        return DB::transaction(function () use ($request, $number, $actor): CustomerRequest {
            $lockedRequest = CustomerRequest::query()->lockForUpdate()->findOrFail($request->id);
            if (! in_array($lockedRequest->status, ['APPROVED', 'PROCESSING'], true)) {
                throw ValidationException::withMessages(['request' => 'inventory.request_not_eligible']);
            }
            if (RfidAssignment::query()->where('customer_request_id', $lockedRequest->id)->exists()) {
                throw ValidationException::withMessages(['rfid_number' => 'inventory.request_already_assigned']);
            }

            $card = RfidCard::query()->where('number', $number)->lockForUpdate()->first();
            if (! $card || $card->status !== 'available') {
                throw ValidationException::withMessages(['rfid_number' => 'inventory.card_unavailable']);
            }

            $card->update(['status' => 'assigned']);
            RfidAssignment::create([
                'rfid_card_id' => $card->id,
                'customer_request_id' => $lockedRequest->id,
                'request_item_id' => null,
                'assigned_by' => $actor->id,
                'assigned_at' => now(),
            ]);
            StockMovement::create([
                'type' => 'OUT',
                'quantity' => 1,
                'rfid_range_id' => $card->rfid_range_id,
                'customer_request_id' => $lockedRequest->id,
                'request_item_id' => null,
                'crm_number' => $lockedRequest->customer()->value('crm_number'),
                'occurred_on' => today(),
                'created_by' => $actor->id,
            ]);
            if ($lockedRequest->status === 'APPROVED') {
                $lockedRequest->update(['status' => 'PROCESSING']);
                RequestStatusHistory::create([
                    'customer_request_id' => $lockedRequest->id,
                    'actor_id' => $actor->id,
                    'from_status' => 'APPROVED',
                    'to_status' => 'PROCESSING',
                    'customer_visible' => true,
                ]);
                CustomerNotification::create([
                    'customer_request_id' => $lockedRequest->id,
                    'type' => 'request.status_changed',
                    'data' => ['request_number' => $lockedRequest->request_number, 'status' => 'PROCESSING'],
                ]);
            }
            Audit::record('inventory.rfid_assigned', $lockedRequest, $actor->id, [
                'rfid_card_id' => $card->id,
            ]);
            $this->lowStockNotifier->notifyIfNeeded();

            return $lockedRequest->fresh();
        }, 3);
    }
}
