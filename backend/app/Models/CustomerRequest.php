<?php

namespace App\Models;

use Database\Factories\CustomerRequestFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $request_number
 * @property string $request_type
 * @property Carbon|null $request_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Customer $customer
 * @property Collection<int, RequestItem> $items
 * @property Collection<int, RequestStatusHistory> $statusHistory
 * @property Collection<int, RequestEvidence> $evidence
 */
final class CustomerRequest extends Model
{
    /** @use HasFactory<CustomerRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id', 'request_number', 'idempotency_key', 'tracking_code_hash', 'tracking_code_ciphertext', 'submitted_customer', 'request_type', 'requested_quantity', 'request_date', 'status',
        'prioritized', 'prioritize_reason', 'prioritized_by', 'prioritized_at',
    ];

    protected $hidden = ['tracking_code_hash', 'tracking_code_ciphertext', 'idempotency_key'];

    protected function casts(): array
    {
        return ['request_date' => 'date', 'requested_quantity' => 'integer', 'prioritized' => 'boolean', 'prioritized_at' => 'datetime', 'submitted_customer' => 'array'];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return HasMany<RequestItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    /** @return HasOne<RfidAssignment, $this> */
    public function assignment(): HasOne
    {
        return $this->hasOne(RfidAssignment::class);
    }

    /** @return HasMany<RequestStatusHistory, $this> */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(RequestStatusHistory::class);
    }

    /** @return HasMany<RequestEvidence, $this> */
    public function evidence(): HasMany
    {
        return $this->hasMany(RequestEvidence::class);
    }

    /** @return HasMany<CustomerNotification, $this> */
    public function notifications(): HasMany
    {
        return $this->hasMany(CustomerNotification::class);
    }
}
