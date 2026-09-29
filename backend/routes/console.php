<?php

use App\Models\CustomerRequest;
use App\Models\ReportExport;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$notifyDueRequests = function (): void {
    $today = today();
    CustomerRequest::query()->whereNotIn('status', ['COMPLETED', 'REJECTED'])->whereDate('request_date', '<=', $today->copy()->subDay())->each(function (CustomerRequest $request) use ($today): void {
        $state = $request->request_date?->lt($today->copy()->subDay()) ? 'LATE' : 'DUE_TODAY';
        User::query()->whereIn('role', ['admin', 'viewer'])->each(fn (User $user) => UserNotification::firstOrCreate([
            'user_id' => $user->id,
            'dedupe_key' => 'request-target:'.$request->id.':'.$today->toDateString().':'.$user->id,
        ], ['type' => 'request.target_alert', 'data' => ['request_id' => $request->id, 'request_number' => $request->request_number, 'state' => $state]]));
    });
};

Artisan::command('requests:notify-targets', $notifyDueRequests)->purpose('Notify authorized operators about due and late requests');
Schedule::call($notifyDueRequests)->dailyAt('08:00');

$pruneReportExports = function (): void {
    ReportExport::query()->where('expires_at', '<', now())->each(function (ReportExport $export): void {
        Storage::disk('local')->delete($export->path);
        $export->delete();
    });
};

Artisan::command('reports:prune-exports', $pruneReportExports)->purpose('Remove expired private report exports');
Schedule::call($pruneReportExports)->dailyAt('02:00');
