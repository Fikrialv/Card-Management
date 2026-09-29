<?php

namespace App\Actions;

use App\Models\CustomerNotification;
use App\Models\CustomerRequest;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReviewCustomerRequest
{
    public function handle(CustomerRequest $request, User $actor, string $decision, ?string $reason = null): CustomerRequest
    {
        return DB::transaction(function () use ($request, $actor, $decision, $reason): CustomerRequest {
            $locked = CustomerRequest::query()->lockForUpdate()->findOrFail($request->id);
            if ($locked->status === $decision) {
                return $locked;
            }
            if ($locked->status !== 'UNDER_REVIEW' || ! in_array($decision, ['APPROVED', 'REJECTED'], true)) {
                throw ValidationException::withMessages(['status' => 'request.invalid_transition']);
            }
            if ($decision === 'REJECTED' && blank($reason)) {
                throw ValidationException::withMessages(['reason' => 'validation.required']);
            }

            $from = $locked->status;
            $locked->update(['status' => $decision]);
            $locked->statusHistory()->create([
                'actor_id' => $actor->id,
                'from_status' => $from,
                'to_status' => $decision,
                'reason' => $reason,
                'customer_visible' => true,
            ]);
            CustomerNotification::create([
                'customer_request_id' => $locked->id,
                'type' => 'request.status_changed',
                'data' => ['request_number' => $locked->request_number, 'status' => $decision, 'reason' => $reason],
            ]);
            Audit::record('request.'.strtolower($decision), $locked, $actor->id, ['from' => $from]);

            return $locked->fresh();
        });
    }
}
