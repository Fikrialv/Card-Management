<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class NotificationController extends Controller
{
    public function markRead(Request $request, UserNotification $userNotification): RedirectResponse
    {
        abort_unless($userNotification->user_id === $request->user()->id, 404);
        $userNotification->update(['read_at' => now()]);

        return redirect()->route('dashboard');
    }
}
