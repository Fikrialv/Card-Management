<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<CustomerRequest> */
final class CustomerRequestFactory extends Factory
{
    protected $model = CustomerRequest::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'request_number' => 'REQ-'.fake()->unique()->numerify('########'),
            'tracking_code_hash' => Hash::make('tracking-test'),
            'submitted_customer' => [
                'institution_name' => 'Test Institution',
                'crm_number' => 'CRM-TEST',
            ],
            'request_type' => 'new_card',
            'requested_quantity' => 1,
            'request_date' => today(),
            'status' => 'NEW',
            'deadline' => today()->addDays(3),
            'urgency' => false,
            'calculated_priority' => 'NORMAL',
        ];
    }
}
