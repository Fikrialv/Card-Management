<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class StockMovement extends Model
{
    protected $fillable = [
        'type', 'quantity', 'rfid_range_id', 'customer_request_id', 'request_item_id',
        'stock_request_id', 'procurement_note_id', 'description', 'crm_number', 'occurred_on', 'created_by',
    ];
}
