<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class RfidCard extends Model
{
    protected $fillable = ['rfid_range_id', 'number', 'status'];
}
