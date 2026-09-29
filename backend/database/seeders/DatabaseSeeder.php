<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\CustomerRequest;
use App\Models\RequestStatusHistory;
use App\Models\RfidCard;
use App\Models\RfidRange;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = collect(['admin', 'viewer'])->mapWithKeys(function (string $role): array {
                $user = User::query()->updateOrCreate(
                    ['email' => "$role@example.test"],
                    ['name' => ucfirst($role).' Demo', 'role' => $role, 'locale' => 'id', 'password' => Hash::make('password')],
                );

                return [$role => $user];
            });

            $customer = Customer::query()->updateOrCreate(
                ['crm_number' => 'CRM-DEMO-001'],
                ['institution_name' => 'Institusi Demonstrasi'],
            );

            $request = CustomerRequest::query()->updateOrCreate(
                ['request_number' => 'DEMO-001'],
                [
                    'customer_id' => $customer->id,
                    'tracking_code_hash' => Hash::make('demo-tracking'),
                    'submitted_customer' => $customer->only(['institution_name', 'crm_number']),
                    'request_type' => 'new_card',
                    'request_date' => today(),
                    'status' => 'NEW',
                    'prioritized' => false,
                ],
            );
            RequestStatusHistory::query()->firstOrCreate(
                ['customer_request_id' => $request->id, 'to_status' => 'NEW'],
                ['actor_id' => $users['admin']->id, 'customer_visible' => true],
            );

            $range = RfidRange::query()->updateOrCreate(
                ['start_number' => 900001, 'end_number' => 900003],
                ['quantity' => 3, 'source' => 'Seed demonstrasi', 'occurred_on' => today(), 'created_by' => $users['admin']->id],
            );
            foreach (range(900001, 900003) as $number) {
                RfidCard::query()->firstOrCreate(['number' => $number], ['rfid_range_id' => $range->id, 'status' => 'available']);
            }
            StockMovement::query()->firstOrCreate(
                ['rfid_range_id' => $range->id, 'type' => 'IN'],
                ['quantity' => 3, 'description' => 'Seed demonstrasi', 'occurred_on' => today(), 'created_by' => $users['admin']->id],
            );
            AuditLog::query()->firstOrCreate(
                ['action' => 'seed.completed', 'auditable_type' => User::class, 'auditable_id' => $users['admin']->id],
                ['actor_id' => $users['admin']->id, 'metadata' => ['scope' => 'development'], 'correlation_id' => '00000000-0000-4000-8000-000000000001'],
            );
        });
    }
}
