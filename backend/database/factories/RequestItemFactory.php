<?php

namespace Database\Factories;

use App\Models\CustomerRequest;
use App\Models\RequestItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RequestItem> */
final class RequestItemFactory extends Factory
{
    protected $model = RequestItem::class;

    public function definition(): array
    {
        return [
            'customer_request_id' => CustomerRequest::factory(),
            'license_plate' => 'B '.fake()->unique()->numerify('####').' TEST',
            'user_name' => fake()->name(),
            'vehicle_type' => 'Mobil',
            'fuel_type' => 'Pertalite',
            'quota_amount' => 100,
            'quota_unit' => 'liter',
            'quota_period' => 'monthly',
        ];
    }
}
