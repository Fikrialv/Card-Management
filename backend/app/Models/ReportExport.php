<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ReportExport extends Model
{
    protected $fillable = ['user_id', 'format', 'path', 'filters', 'expires_at'];

    protected function casts(): array
    {
        return ['filters' => 'array', 'expires_at' => 'datetime'];
    }
}
