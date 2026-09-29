<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class RequestStatusHistory extends Model
{
    protected $fillable = ['customer_request_id', 'actor_id', 'from_status', 'to_status', 'reason', 'customer_visible'];

    protected function casts(): array
    {
        return ['customer_visible' => 'boolean'];
    }
}
