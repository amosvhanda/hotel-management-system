<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\GuestMessenger;
use Illuminate\Console\Command;

/**
 * Timed guest emails (runs every 15 minutes from the scheduler, Harare time):
 *   pre_arrival    — check-in in 2 days, from 09:00
 *   arrival_day    — check-in today, from 07:00
 *   review_request — checked out 1–7 days ago, from 10:00, not yet reviewed
 * Each is sent once per booking (message_logs), so re-runs are safe.
 */
class SendGuestMessages extends Command
{
    protected $signature = 'guests:send-messages';

    protected $description = 'Queue pre-arrival, arrival-day and rate-your-stay emails that are due';

    public function handle(GuestMessenger $messenger): int
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $active = fn () => Booking::with('room.type')->where('status', '!=', Booking::STATUS_CANCELLED);
        $count = 0;

        if ($now->hour >= 9) {
            foreach ($active()->whereDate('check_in', $today->copy()->addDays(2))->get() as $b) {
                $count += $messenger->send($b, 'pre_arrival') === 'queued';
            }
        }

        if ($now->hour >= 7) {
            foreach ($active()->whereDate('check_in', $today)->get() as $b) {
                $count += $messenger->send($b, 'arrival_day') === 'queued';
            }
        }

        if ($now->hour >= 10) {
            $recent = $active()->whereDate('check_out', '<', $today)->whereDate('check_out', '>=', $today->copy()->subDays(7))
                ->whereDoesntHave('review')->get();
            foreach ($recent as $b) {
                $count += $messenger->send($b, 'review_request') === 'queued';
            }
        }

        $this->info("Queued {$count} guest message(s).");

        return self::SUCCESS;
    }
}
