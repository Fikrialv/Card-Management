<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class RequestEvidence extends Model
{
    protected $table = 'request_evidence';

    protected $fillable = ['customer_request_id', 'request_item_id', 'artifact_type', 'path', 'original_name', 'mime_type', 'size'];
}
