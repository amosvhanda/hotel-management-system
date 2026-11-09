<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\StaffAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/** Send a bell alert to every admin. Never breaks the guest's request if it fails. */
class StaffAlerts
{
    public static function send(string $kind, string $message, string $url, string $icon = 'bx-bell'): void
    {
        try {
            Notification::send(User::where('role', 'admin')->get(), new StaffAlert($kind, $message, $url, $icon));
        } catch (\Throwable $e) {
            Log::warning('Staff alert failed', ['kind' => $kind, 'error' => $e->getMessage()]);
        }
    }
}
