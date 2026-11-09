<?php

namespace App\Jobs;

use App\Mail\GuestMessageMail;
use App\Models\Booking;
use App\Models\MessageLog;
use App\Models\SiteSetting;
use App\Providers\AppServiceProvider;
use App\Support\GuestMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

/** Sends one logged guest email in the background; retried 3 times, then shown under Guest messages → Failed. */
class SendGuestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public int $messageLogId)
    {
    }

    public function handle(): void
    {
        $log = MessageLog::find($this->messageLogId);
        if (! $log || $log->status === 'sent') {
            return;
        }
        $booking = Booking::with('room.type')->find($log->booking_id);
        if (! $booking) {
            $log->update(['status' => 'skipped', 'error' => 'Booking no longer exists']);

            return;
        }

        // Staff may have changed SMTP in Admin since the worker started.
        SiteSetting::flushCurrent();
        AppServiceProvider::applySmtpSettings();
        Mail::purge();

        $t = GuestMessages::get($log->template);
        $values = GuestMessages::values($booking);

        Mail::to($log->recipient)->send(new GuestMessageMail(
            GuestMessages::fill($t['subject'], $values),
            GuestMessages::fill($t['body'], $values),
            $t['button'] ?? null,
            GuestMessages::link($log->template, $booking),
        ));

        $log->update(['status' => 'sent', 'sent_at' => now(), 'error' => null]);
    }

    public function failed(\Throwable $e): void
    {
        MessageLog::whereKey($this->messageLogId)->update(['status' => 'failed', 'error' => mb_substr($e->getMessage(), 0, 1000)]);
    }
}
