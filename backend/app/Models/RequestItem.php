<?php

namespace App\Models;

use Database\Factories\RequestItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class RequestItem extends Model
{
    /** @use HasFactory<RequestItemFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_request_id', 'rfid_number', 'license_plate', 'user_name', 'vehicle_type',
        'fuel_type', 'quota_amount', 'quota_unit', 'quota_period',
        'balance_transfer_amount', 'destination_code', 'activation_requested', 'change_type',
    ];

    protected function casts(): array
    {
        return ['activation_requested' => 'boolean', 'quota_amount' => 'decimal:2', 'balance_transfer_amount' => 'decimal:2'];
    }

    /** @return BelongsTo<CustomerRequest, $this> */
    public function customerRequest(): BelongsTo
    {
        return $this->belongsTo(CustomerRequest::class);
    }

    /** @return HasOne<RfidAssignment, $this> */
    public function assignment(): HasOne
    {
        return $this->hasOne(RfidAssignment::class);
    }
}
