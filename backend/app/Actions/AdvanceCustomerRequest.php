<?php

namespace App\Actions;

use App\Models\CustomerNotification;
use App\Models\CustomerRequest;
use App\Models\RfidAssignment;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdvanceCustomerRequest
{
    public function handle(CustomerRequest $request, string $target, User $actor): CustomerRequest
    {
        return DB::transaction(function () use ($request, $target, $actor): CustomerRequest {
            $locked = CustomerRequest::query()->lockForUpdate()->findOrFail($request->id);

            if ($locked->status === $target) {
                return $locked;
            }

            $expected = match ($target) {
                'UNDER_REVIEW' => 'NEW',
                'COMPLETED' => 'PROCESSING',
                default => null,
            };
            if ($expected === null || $locked->status !== $expected) {
                throw ValidationException::withMessages(['status' => 'request.invalid_transition']);
            }
            if ($target === 'COMPLETED' && ! RfidAssignment::query()->where('customer_request_id', $locked->id)->exists()) {
                throw ValidationException::withMessages(['rfid_number' => 'inventory.assignment_incomplete']);
            }

            $from = $locked->status;
            $locked->update(['status' => $target]);
            $locked->statusHistory()->create([
                'actor_id' => $actor->id,
                'from_status' => $from,
                'to_status' => $target,
                'customer_visible' => true,
            ]);
            CustomerNotification::create([
                'customer_request_id' => $locked->id,
                'type' => 'request.status_changed',
                'data' => ['request_number' => $locked->request_number, 'status' => $target],
            ]);
            Audit::record('request.'.strtolower($target), $locked, $actor->id, ['from' => $from]);

            return $locked->fresh();
        });
    }
}
