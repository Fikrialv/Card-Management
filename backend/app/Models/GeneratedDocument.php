<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GeneratedDocument extends Model
{
    protected $fillable = ['document_template_id', 'document_number', 'locale', 'input', 'path', 'sha256', 'created_by'];

    protected function casts(): array
    {
        return ['input' => 'array'];
    }
}
