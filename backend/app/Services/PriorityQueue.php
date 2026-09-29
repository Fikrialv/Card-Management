<?php

namespace App\Services;

use App\Models\CustomerRequest;
use Illuminate\Database\Eloquent\Builder;

final class PriorityQueue
{
    /**
     * @param  array<string, string|null>  $filters
     * @return Builder<CustomerRequest>
     */
    public function ordered(array $filters = []): Builder
    {
        $today = today()->toDateString();

        return CustomerRequest::query()
            ->with(['customer'])
            ->when(empty($filters['status']), fn (Builder $query) => $query->whereIn('status', ['NEW', 'UNDER_REVIEW']))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when(isset($filters['prioritized']) && $filters['prioritized'] !== '', fn (Builder $query) => $query->where('prioritized', filter_var($filters['prioritized'], FILTER_VALIDATE_BOOLEAN)))
            ->when($filters['request_type'] ?? null, fn (Builder $query, string $type) => $query->where('request_type', $type))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('request_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('request_date', '<=', $date))
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
                $query->where(function (Builder $query) use ($term): void {
                    $query->where('request_number', 'like', $term)
                        ->orWhereHas('customer', fn (Builder $customer) => $customer
                            ->where('institution_name', 'like', $term)
                            ->orWhere('crm_number', 'like', $term));
                });
            })
            ->orderByDesc('prioritized')
            ->orderByRaw("CASE WHEN status NOT IN ('COMPLETED', 'REJECTED') AND request_date < ? THEN 0 ELSE 1 END", [$today])
            ->orderBy('request_date')
            ->orderBy('created_at')
            ->orderBy('id');
    }
}
