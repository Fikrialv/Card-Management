<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property array<string, mixed> $data
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 */
final class UserNotification extends Model
{
    protected $fillable = ['user_id', 'type', 'dedupe_key', 'data', 'read_at'];

    protected function casts(): array
    {
        return ['data' => 'array', 'read_at' => 'datetime'];
    }
}
