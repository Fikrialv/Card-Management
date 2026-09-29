<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RfidAssignment extends Model
{
    protected $fillable = ['rfid_card_id', 'customer_request_id', 'request_item_id', 'assigned_by', 'assigned_at'];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime'];
    }

    /** @return BelongsTo<RfidCard, $this> */
    public function card(): BelongsTo
    {
        return $this->belongsTo(RfidCard::class, 'rfid_card_id');
    }
}
