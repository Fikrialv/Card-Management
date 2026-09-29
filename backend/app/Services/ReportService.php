<?php

namespace App\Services;

use App\Models\CustomerRequest;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Builder;

final class ReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<CustomerRequest>
     */
    public function requests(array $filters = []): Builder
    {
        return CustomerRequest::query()->with('customer')->when($filters['date_from'] ?? null, fn (Builder $q, string $v) => $q->whereDate('request_date', '>=', $v))->when($filters['date_to'] ?? null, fn (Builder $q, string $v) => $q->whereDate('request_date', '<=', $v))->when($filters['status'] ?? null, fn (Builder $q, string $v) => $q->where('status', $v))->when($filters['request_type'] ?? null, fn (Builder $q, string $v) => $q->where('request_type', $v))->oldest('request_date')->oldest('created_at')->oldest('id');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function summary(array $filters = []): array
    {
        $requests = $this->requests($filters)->get();
        $movements = StockMovement::query()->when($filters['date_from'] ?? null, fn (Builder $q, string $v) => $q->whereDate('occurred_on', '>=', $v))->when($filters['date_to'] ?? null, fn (Builder $q, string $v) => $q->whereDate('occurred_on', '<=', $v));

        return ['total_requests' => $requests->count(), 'by_status' => $requests->groupBy('status')->map->count()->all(), 'stock_in' => (int) $movements->clone()->where('type', 'IN')->sum('quantity'), 'stock_out' => (int) $movements->clone()->where('type', 'OUT')->sum('quantity')];
    }
}
