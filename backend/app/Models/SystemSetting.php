<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'updated_by'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }
}
