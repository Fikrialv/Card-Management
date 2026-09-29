<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $customer_request_id
 * @property string $type
 * @property array<string, mixed> $data
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 */
final class CustomerNotification extends Model
{
    protected $fillable = ['customer_request_id', 'type', 'data', 'read_at'];

    protected function casts(): array
    {
        return ['data' => 'array', 'read_at' => 'datetime'];
    }
}
