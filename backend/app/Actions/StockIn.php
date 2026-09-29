<?php

namespace App\Actions;

use App\Models\RfidCard;
use App\Models\RfidRange;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class StockIn
{
    public function handleQuantity(int $quantity, string $source, string $occurredOn, User $actor, ?int $procurementNoteId = null): RfidRange
    {
        $next = ((int) RfidCard::query()->max('number')) + 1;

        return $this->handle(['start_number' => $next, 'end_number' => $next + $quantity - 1, 'source' => $source, 'occurred_on' => $occurredOn], $actor, null, $procurementNoteId);
    }

    /** @param array{start_number: int|string, end_number: int|string, source: string, occurred_on: string} $data */
    public function handle(array $data, User $actor, ?int $stockRequestId = null, ?int $procurementNoteId = null): RfidRange
    {
        return DB::transaction(function () use ($data, $actor, $stockRequestId, $procurementNoteId): RfidRange {
            $start = (int) $data['start_number'];
            $end = (int) $data['end_number'];
            $quantity = $end - $start + 1;

            if ($end < $start || $quantity > 5000) {
                throw ValidationException::withMessages(['end_number' => 'inventory.invalid_range']);
            }

            $overlap = RfidRange::query()
                ->where('start_number', '<=', $end)
                ->where('end_number', '>=', $start)
                ->lockForUpdate()
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages(['start_number' => 'inventory.range_overlap']);
            }

            $range = RfidRange::create([
                'start_number' => $start,
                'end_number' => $end,
                'quantity' => $quantity,
                'source' => $data['source'],
                'occurred_on' => $data['occurred_on'],
                'created_by' => $actor->id,
                'stock_request_id' => $stockRequestId,
                'procurement_note_id' => $procurementNoteId,
            ]);

            $now = now();
            $rows = [];
            for ($number = $start; $number <= $end; $number++) {
                $rows[] = [
                    'rfid_range_id' => $range->id,
                    'number' => $number,
                    'status' => 'available',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            RfidCard::query()->insert($rows);
            StockMovement::create([
                'type' => 'IN',
                'quantity' => $quantity,
                'rfid_range_id' => $range->id,
                'stock_request_id' => $stockRequestId,
                'procurement_note_id' => $procurementNoteId,
                'description' => $data['source'],
                'occurred_on' => $data['occurred_on'],
                'created_by' => $actor->id,
            ]);
            Audit::record('inventory.stock_in', $range, $actor->id, ['quantity' => $quantity]);

            return $range;
        }, 3);
    }
}
