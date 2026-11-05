<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingComplete extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** Stored with everything the bell needs, so it never has to look the booking up again. */
    public function toArray(object $notifiable): array
    {
        $b = $this->booking;
        $paid = (int) $b->payment_status === 1;

        return [
            'type' => $paid ? 'booking_paid' : 'booking_new',
            'message' => ($paid ? 'Paid booking' : 'New booking') . " #{$b->code} from {$b->name}",
            'booking_id' => $b->id,
            'code' => $b->code,
            'guest' => $b->name,
            'total' => (float) $b->total_price,
            'check_in' => $b->check_in,
            'check_out' => $b->check_out,
            'paid' => $paid,
        ];
    }
}
