<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Customer> */
final class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'institution_name' => $name,
            'normalized_institution_name' => trim((string) preg_replace('/\s+/', ' ', (string) preg_replace('/[^A-Z0-9]+/i', ' ', strtoupper($name)))),
            'crm_number' => 'CRM'.fake()->unique()->numerify('######'),
        ];
    }
}
