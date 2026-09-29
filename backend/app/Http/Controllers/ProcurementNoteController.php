<?php

namespace App\Http\Controllers;

use App\Actions\ReceiveProcurementNote;
use App\Http\Requests\ReceiveProcurementNoteRequest;
use App\Http\Requests\StoreProcurementNoteRequest;
use App\Models\ProcurementNote;
use App\Support\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class ProcurementNoteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ProcurementNotes/Index', [
            'procurementNotes' => ProcurementNote::query()->latest()->paginate(25),
        ]);
    }

    public function store(StoreProcurementNoteRequest $request): JsonResponse|RedirectResponse
    {
        $note = DB::transaction(function () use ($request): ProcurementNote {
            $note = ProcurementNote::create([
                ...$request->validated(),
                'reference_number' => 'PROC-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'status' => 'DRAFT',
                'created_by' => $request->user()->id,
            ]);
            Audit::record('procurement_note.created', $note, $request->user()->id);

            return $note;
        });

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['data' => $note], 201);
    }

    public function receive(ReceiveProcurementNoteRequest $request, ProcurementNote $procurementNote, ReceiveProcurementNote $action): JsonResponse|RedirectResponse
    {
        $note = $action->handle($procurementNote, $request->validated(), $request->user());

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['data' => $note]);
    }

    public function destroy(Request $request, ProcurementNote $procurementNote): RedirectResponse|JsonResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);
        abort_if((int) $procurementNote->received_quantity > 0 || $procurementNote->status !== 'DRAFT', 422, 'Catatan yang sudah diterima tidak dapat dihapus.');

        DB::transaction(function () use ($procurementNote, $request): void {
            Audit::record('procurement_note.deleted', $procurementNote, $request->user()->id, [
                'reference_number' => $procurementNote->reference_number,
            ]);
            $procurementNote->delete();
        });

        return $request->header('X-Inertia') ? redirect()->back(303) : response()->json(['deleted' => true]);
    }
}
