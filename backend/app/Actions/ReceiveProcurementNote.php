<?php

namespace App\Actions;

use App\Models\ProcurementNote;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReceiveProcurementNote
{
    public function __construct(private readonly StockIn $stockIn) {}

    /** @param array{received_on: string, received_quantity: int|string} $data */
    public function handle(ProcurementNote $note, array $data, User $actor): ProcurementNote
    {
        return DB::transaction(function () use ($note, $data, $actor): ProcurementNote {
            $note = ProcurementNote::query()->lockForUpdate()->findOrFail($note->id);
            $received = (int) $note->received_quantity;
            $quantity = (int) $data['received_quantity'];
            if ($note->status === 'RECEIVED' || $received + $quantity > (int) $note->requested_quantity) {
                throw ValidationException::withMessages(['received_quantity' => 'procurement.receipt_exceeds_request']);
            }

            $this->stockIn->handleQuantity($quantity, "Catatan pengadaan {$note->reference_number}", $data['received_on'], $actor, $note->id);

            $note->update([
                'received_on' => $data['received_on'],
                'received_quantity' => $received + $quantity,
                'received_by' => $actor->id,
                'status' => $received + $quantity === (int) $note->requested_quantity ? 'RECEIVED' : 'PARTIAL',
            ]);
            User::query()->where('role', 'admin')->each(fn (User $user) => UserNotification::create([
                'user_id' => $user->id,
                'type' => 'inventory.procurement_received',
                'data' => ['procurement_note_id' => $note->id, 'reference_number' => $note->reference_number],
            ]));
            Audit::record('procurement_note.received', $note, $actor->id, ['quantity' => $quantity]);

            return $note->fresh();
        }, 3);
    }
}
