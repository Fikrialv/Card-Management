<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $fillable = ['institution_name', 'normalized_institution_name', 'crm_number'];

    /** @return HasMany<CustomerRequest, $this> */
    public function requests(): HasMany
    {
        return $this->hasMany(CustomerRequest::class);
    }
}
