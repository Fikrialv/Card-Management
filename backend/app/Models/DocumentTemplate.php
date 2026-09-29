<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class DocumentTemplate extends Model
{
    protected $fillable = ['name', 'version', 'locale', 'status', 'schema', 'content', 'created_by'];

    protected function casts(): array
    {
        return ['schema' => 'array'];
    }
}
