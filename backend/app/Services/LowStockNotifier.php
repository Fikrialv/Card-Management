<?php

namespace App\Services;

use App\Models\RfidCard;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserNotification;

final class LowStockNotifier
{
    public function threshold(): int
    {
        $setting = SystemSetting::query()->where('key', 'inventory.low_stock_threshold')->value('value');

        return max(0, (int) ($setting['threshold'] ?? config('rfid.low_stock_threshold')));
    }

    public function notifyIfNeeded(): void
    {
        $available = RfidCard::query()->where('status', 'available')->count();
        $threshold = $this->threshold();
        if ($available > $threshold) {
            return;
        }

        $date = today()->toDateString();
        User::query()->where('role', 'admin')->each(function (User $user) use ($available, $threshold, $date): void {
            UserNotification::query()->firstOrCreate(
                ['dedupe_key' => "inventory.low_stock:{$date}:{$user->id}"],
                [
                    'user_id' => $user->id,
                    'type' => 'inventory.low_stock',
                    'data' => ['available' => $available, 'threshold' => $threshold],
                ],
            );
        });
    }
}
