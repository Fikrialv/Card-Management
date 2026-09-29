<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProcurementNote extends Model
{
    protected $fillable = [
        'reference_number', 'request_date', 'pic_name', 'requested_quantity', 'notes',
        'received_on', 'received_quantity', 'status', 'created_by', 'received_by',
    ];

    protected function casts(): array
    {
        return ['request_date' => 'date', 'received_on' => 'date'];
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
