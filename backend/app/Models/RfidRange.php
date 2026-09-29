<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class RfidRange extends Model
{
    protected $fillable = ['start_number', 'end_number', 'quantity', 'source', 'occurred_on', 'created_by', 'stock_request_id', 'procurement_note_id'];

    protected function casts(): array
    {
        return ['occurred_on' => 'date'];
    }

    /** @return HasMany<RfidCard, $this> */
    public function cards(): HasMany
    {
        return $this->hasMany(RfidCard::class);
    }
}
