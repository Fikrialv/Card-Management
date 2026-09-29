<?php

namespace App\Http\Resources;

use App\Models\CustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CustomerRequest */
final class TrackingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'request_number' => $this->request_number,
            'request_type' => $this->request_type,
            'status' => $this->status,
            'updated_at' => $this->updated_at?->toIso8601String(),
            'timeline' => $this->statusHistory->where('customer_visible', true)->map(fn ($history) => [
                'status' => $history->to_status,
                'reason' => $history->reason,
                'at' => $history->created_at?->toIso8601String(),
            ])->values(),
            'notifications' => $this->whenLoaded('notifications', fn () => $this->notifications->map(fn ($notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ])->values()),
        ];
    }
}
