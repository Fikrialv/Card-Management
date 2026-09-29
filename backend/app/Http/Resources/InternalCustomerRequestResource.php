<?php

namespace App\Http\Resources;

use App\Models\CustomerRequest;
use App\Services\CustomerIdentity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CustomerRequest */
final class InternalCustomerRequestResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'request_type' => $this->request_type,
            'requested_quantity' => $this->requested_quantity,
            'status' => $this->status,
            'request_date' => $this->request_date?->toDateString(),
            'prioritized' => $this->prioritized,
            'target_date' => $this->request_date?->copy()->addDay()->toDateString(),
            'target_state' => $this->targetState(),
            'customer' => $this->whenLoaded('customer', fn () => [
                'institution_name' => $this->customer->institution_name,
                'crm_number' => $this->customer->crm_number,
                'duplicate_name_warning' => app(CustomerIdentity::class)->hasDuplicateName($this->customer->institution_name, $this->customer->crm_number),
            ]),
            'assignment' => $this->whenLoaded('assignment', fn () => [
                'id' => $this->assignment->id,
                'rfid_number' => $this->assignment->card?->number,
            ]),
            'status_history' => $this->whenLoaded('statusHistory'),
            'evidence' => $this->whenLoaded('evidence', fn () => $this->evidence->map(fn ($evidence) => [
                'id' => $evidence->id,
                'name' => $evidence->original_name,
                'mime_type' => $evidence->mime_type,
                'size' => $evidence->size,
                'download_url' => $request->user()?->role === 'admin' ? route('request-evidence.download', $evidence) : null,
            ])->values()->all()),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    private function targetState(): string
    {
        if (in_array($this->status, ['COMPLETED', 'REJECTED'], true) || ! $this->request_date) {
            return 'DONE';
        }
        if ($this->request_date->isBefore(today()->subDay())) {
            return 'LATE';
        }
        if ($this->request_date->isYesterday()) {
            return 'DUE_TODAY';
        }

        return 'ON_TRACK';
    }
}
