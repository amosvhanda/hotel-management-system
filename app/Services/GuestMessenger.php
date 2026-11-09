<?php

namespace App\Services;

use App\Jobs\SendGuestEmail;
use App\Models\Booking;
use App\Models\MessageLog;
use App\Models\SiteSetting;
use App\Support\GuestMessages;
use Illuminate\Support\Facades\Log;

/**
 * Queue a guest message for a booking. Each template goes at most once per booking
 * (message_logs has a unique key), is skipped when switched off, and never throws.
 */
class GuestMessenger
{
    /** @return string queued | already_sent | disabled | no_recipient | error */
    public function send(Booking $booking, string $template, bool $resend = false): string
    {
        try {
            if (! array_key_exists($template, GuestMessages::TEMPLATES)) {
                return 'error';
            }
            if (! GuestMessages::enabled($template)) {
                return 'disabled';
            }

            $to = GuestMessages::TEMPLATES[$template]['to'] === 'hotel' ? SiteSetting::current()->email : $booking->email;
            if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
                return 'no_recipient';
            }

            $log = MessageLog::firstOrNew(['booking_id' => $booking->id, 'channel' => 'email', 'template' => $template]);
            if ($log->exists && in_array($log->status, ['sent', 'queued'], true) && ! $resend) {
                return 'already_sent';
            }
            $log->fill(['recipient' => $to, 'status' => 'queued', 'error' => null])->save();

            SendGuestEmail::dispatch($log->id);

            return 'queued';
        } catch (\Throwable $e) {
            Log::warning('Guest message could not be queued', ['booking' => $booking->id, 'template' => $template, 'error' => $e->getMessage()]);

            return 'error';
        }
    }

    /** Guest email + copy to the hotel for a new booking / payment. */
    public function newBooking(Booking $booking, string $guestTemplate): void
    {
        $this->send($booking, $guestTemplate);
        $this->send($booking, 'staff_new_booking');
    }
}
