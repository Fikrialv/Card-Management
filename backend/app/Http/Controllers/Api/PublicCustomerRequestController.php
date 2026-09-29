<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateCustomerRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequestRequest;
use App\Http\Requests\TrackCustomerRequestRequest;
use App\Http\Resources\TrackingResource;
use App\Models\CustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class PublicCustomerRequestController extends Controller
{
    public function requestTemplate(): BinaryFileResponse
    {
        $path = base_path('../Data/Template RFID 2025.xlsx');
        abort_unless(is_file($path), 404);

        return response()->download($path, 'Template RFID 2025.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ETag' => hash_file('sha256', $path),
            'X-Template-Version' => '2025',
        ]);
    }

    public function store(StoreCustomerRequestRequest $request, CreateCustomerRequest $action): JsonResponse
    {
        $idempotencyKey = $request->header('Idempotency-Key');
        abort_if($idempotencyKey !== null && ! preg_match('/^[A-Za-z0-9_-]{16,64}$/', $idempotencyKey), 422, 'request.invalid_idempotency_key');
        $result = $action->handle($request->validated(), [
            'application' => $request->file('application_file'),
            'payment_proof' => $request->file('payment_proof'),
        ], $idempotencyKey);

        return response()->json(['data' => [
            'request_number' => $result['request']->request_number,
            'tracking_code' => $result['tracking_code'],
            'status' => $result['request']->status,
        ]], 201);
    }

    public function requirements(Request $request): JsonResponse
    {
        $data = $request->validate([
            'crm_number' => ['nullable', 'string', 'regex:/^[A-Za-z0-9]{1,13}$/'],
            'institution_name' => ['nullable', 'string', 'max:160'],
            'requested_quantity' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ]);

        return response()->json(['data' => ['payment_required' => false, 'duplicate_name_warning' => false]]);
    }

    public function track(TrackCustomerRequestRequest $request): JsonResponse|TrackingResource
    {
        $customerRequest = CustomerRequest::query()->with(['statusHistory', 'notifications'])
            ->where('request_number', $request->validated('request_number'))->first();
        if (! $customerRequest || ! Hash::check($request->validated('tracking_code'), $customerRequest->tracking_code_hash)) {
            return response()->json(['message' => 'tracking.not_found'], 404);
        }

        return new TrackingResource($customerRequest);
    }

    public function markNotificationRead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'request_number' => ['required', 'string', 'max:32'],
            'tracking_code' => ['required', 'string', 'max:128'],
            'notification_id' => ['required', 'integer'],
        ]);
        $customerRequest = CustomerRequest::query()->where('request_number', $data['request_number'])->first();
        if (! $customerRequest || ! Hash::check($data['tracking_code'], $customerRequest->tracking_code_hash)) {
            return response()->json(['message' => 'tracking.not_found'], 404);
        }

        $notification = $customerRequest->notifications()->whereKey($data['notification_id'])->first();
        if (! $notification) {
            return response()->json(['message' => 'tracking.not_found'], 404);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(status: 204);
    }
}
