<?php

namespace App\Http\Controllers;

use App\Actions\AdvanceCustomerRequest;
use App\Actions\ReviewCustomerRequest;
use App\Http\Resources\InternalCustomerRequestResource;
use App\Models\CustomerRequest;
use App\Models\RequestEvidence;
use App\Services\PriorityQueue;
use App\Support\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CustomerRequestController extends Controller
{
    public function index(Request $request, PriorityQueue $queue): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:NEW,UNDER_REVIEW,APPROVED,REJECTED,PROCESSING,COMPLETED'],
            'prioritized' => ['nullable', 'boolean'],
            'request_type' => ['nullable', 'string', 'max:32'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return Inertia::render('Requests/Index', [
            'requests' => InternalCustomerRequestResource::collection($queue->ordered($filters)->paginate(25)->withQueryString()),
            'filters' => $filters,
        ]);
    }

    public function show(CustomerRequest $customerRequest): Response
    {
        return Inertia::render('Requests/Show', [
            'requestRecord' => new InternalCustomerRequestResource($customerRequest->load(['customer', 'assignment.card', 'statusHistory', 'evidence'])),
        ]);
    }

    public function downloadEvidence(Request $request, RequestEvidence $requestEvidence): StreamedResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);
        abort_unless(Storage::disk('local')->exists($requestEvidence->path), 404);

        return Storage::disk('local')->download($requestEvidence->path, $requestEvidence->original_name);
    }

    public function approve(Request $request, CustomerRequest $customerRequest, ReviewCustomerRequest $action): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->canOperateRequests(), 403);

        $updated = $action->handle($customerRequest, $request->user(), 'APPROVED');

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['data' => $updated]);
    }

    public function startReview(Request $request, CustomerRequest $customerRequest, AdvanceCustomerRequest $action): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->canOperateRequests(), 403);

        $updated = $action->handle($customerRequest, 'UNDER_REVIEW', $request->user());

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['data' => $updated]);
    }

    public function complete(Request $request, CustomerRequest $customerRequest, AdvanceCustomerRequest $action): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->canManageInventory(), 403);

        $updated = $action->handle($customerRequest, 'COMPLETED', $request->user());

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['data' => $updated]);
    }

    public function reject(Request $request, CustomerRequest $customerRequest, ReviewCustomerRequest $action): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->canOperateRequests(), 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $updated = $action->handle($customerRequest, $request->user(), 'REJECTED', $data['reason']);

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['data' => $updated]);
    }

    public function prioritize(Request $request, CustomerRequest $customerRequest): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->canOperateRequests(), 403);
        $data = $request->validate(['prioritized' => ['required', 'boolean'], 'reason' => ['nullable', 'string', 'max:500']]);
        DB::transaction(function () use ($customerRequest, $data, $request): void {
            $customerRequest->update(['prioritized' => $data['prioritized'], 'prioritize_reason' => $data['reason'] ?? null, 'prioritized_by' => $request->user()->id, 'prioritized_at' => now()]);
            Audit::record('request.prioritized', $customerRequest, $request->user()->id, ['prioritized' => $data['prioritized']]);
        });

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['data' => $customerRequest->fresh()]);
    }
}
