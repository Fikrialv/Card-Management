<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\CustomerNotification;
use App\Models\CustomerRequest;
use App\Models\RequestEvidence;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\CustomerIdentity;
use App\Services\MalwareScanner;
use App\Support\Audit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class CreateCustomerRequest
{
    public function __construct(private readonly MalwareScanner $malwareScanner, private readonly CustomerIdentity $customerIdentity) {}

    /**
     * @param  array{crm_number: string, institution_name: string, request_type: string, request_date: string, requested_quantity?: int}  $data
     * @param  array<string, UploadedFile|null>  $files
     * @return array{request: CustomerRequest, tracking_code: string}
     */
    public function handle(array $data, array $files, ?string $idempotencyKey = null): array
    {
        $stored = [];
        try {
            foreach (array_filter($files) as $file) {
                $this->malwareScanner->assertClean($file);
            }

            return DB::transaction(function () use ($data, $files, $idempotencyKey, &$stored): array {
                if ($idempotencyKey) {
                    $existing = CustomerRequest::query()->where('idempotency_key', $idempotencyKey)->first();
                    if ($existing) {
                        return ['request' => $existing, 'tracking_code' => Crypt::decryptString($existing->tracking_code_ciphertext)];
                    }
                }

                $customer = Customer::query()->firstOrCreate(
                    ['crm_number' => strtoupper(trim($data['crm_number']))],
                    [
                        'institution_name' => trim($data['institution_name']),
                        'normalized_institution_name' => $this->customerIdentity->normalizeName($data['institution_name']),
                    ],
                );
                if (! $customer->normalized_institution_name) {
                    $customer->update(['normalized_institution_name' => $this->customerIdentity->normalizeName($customer->institution_name)]);
                }
                $trackingCode = Str::random(32);
                $request = CustomerRequest::create([
                    'customer_id' => $customer->id,
                    'request_number' => 'RFID-'.now()->format('Ymd').'-'.strtoupper(Str::random(8)),
                    'idempotency_key' => $idempotencyKey,
                    'tracking_code_hash' => Hash::make($trackingCode),
                    'tracking_code_ciphertext' => Crypt::encryptString($trackingCode),
                    'submitted_customer' => [
                        'institution_name' => trim($data['institution_name']),
                        'crm_number' => strtoupper(trim($data['crm_number'])),
                    ],
                    'request_type' => $data['request_type'],
                    'requested_quantity' => (int) ($data['requested_quantity'] ?? 1),
                    'request_date' => $data['request_date'],
                    'status' => 'NEW',
                ]);

                foreach (array_filter($files) as $artifactType => $file) {
                    RequestEvidence::create([
                        'customer_request_id' => $request->id,
                        'artifact_type' => $artifactType,
                        'path' => tap($file->store('request-evidence', 'local'), fn (string $path) => $stored[] = $path),
                        'original_name' => basename($file->getClientOriginalName()),
                        'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                        'size' => $file->getSize(),
                    ]);
                }

                $request->statusHistory()->create(['to_status' => 'NEW', 'customer_visible' => true]);
                CustomerNotification::create([
                    'customer_request_id' => $request->id,
                    'type' => 'request.received',
                    'data' => ['request_number' => $request->request_number, 'status' => 'NEW'],
                ]);
                User::query()->where('role', 'admin')->each(
                    fn (User $user) => UserNotification::create([
                        'user_id' => $user->id,
                        'type' => 'request.created',
                        'data' => ['request_id' => $request->id, 'request_number' => $request->request_number],
                    ]),
                );
                Audit::record('request.created', $request, null, ['request_number' => $request->request_number]);

                return ['request' => $request->fresh(), 'tracking_code' => $trackingCode];
            });
        } catch (Throwable $exception) {
            array_walk($stored, static fn (string $path): bool => Storage::disk('local')->delete($path));
            throw $exception;
        }
    }
}
