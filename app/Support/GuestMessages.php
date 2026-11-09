<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\SiteSetting;
use Carbon\Carbon;

/**
 * The guest messages the hotel sends, their default wording, and placeholder filling.
 * Staff can switch each one off or edit subject/body in Admin → Settings → Guest messages.
 */
class GuestMessages
{
    /** key => [label, when, to, subject, body, button] */
    public const TEMPLATES = [
        'booking_received' => [
            'label' => 'Booking received', 'when' => 'Right after a cash booking is made', 'to' => 'guest',
            'subject' => 'Booking #{code} received — {hotel}',
            'body' => "Hi {guest},\n\nThank you for booking with {hotel}. Here are your details:\n\n{summary}\n\nPayment: {payment}.\n\nWe'll confirm your room shortly. Questions? Reply to this email, call {hotel_phone} or WhatsApp us.",
            'button' => 'View my booking',
        ],
        'payment_received' => [
            'label' => 'Payment received', 'when' => 'When a Paynow / EcoCash payment clears', 'to' => 'guest',
            'subject' => 'Payment received — booking #{code}',
            'body' => "Hi {guest},\n\nWe've received your payment of {total}. Your stay is booked:\n\n{summary}\n\nYour invoice is available in My Bookings. We look forward to welcoming you!",
            'button' => 'View my booking',
        ],
        'booking_confirmed' => [
            'label' => 'Booking confirmed', 'when' => 'When staff confirm a booking', 'to' => 'guest',
            'subject' => 'Your stay at {hotel} is confirmed — #{code}',
            'body' => "Hi {guest},\n\nGood news — your booking is confirmed.\n\n{summary}\n\nPayment: {payment}.\n\nSee you soon!",
            'button' => 'View my booking',
        ],
        'booking_cancelled' => [
            'label' => 'Booking cancelled', 'when' => 'When a booking is cancelled or a payment fails', 'to' => 'guest',
            'subject' => 'Booking #{code} cancelled',
            'body' => "Hi {guest},\n\nYour booking #{code} for {room} ({check_in} – {check_out}) has been cancelled.\n\nIf this wasn't you, or you'd like to book again, reply to this email or call {hotel_phone}.",
            'button' => 'Book again',
        ],
        'pre_arrival' => [
            'label' => 'Pre-arrival reminder', 'when' => '2 days before check-in, from 09:00', 'to' => 'guest',
            'subject' => 'See you in 2 days, {guest}!',
            'body' => "Hi {guest},\n\nWe're getting ready for your stay:\n\n{summary}\n\nCheck-in is from 14:00 and check-out is by 10:00. We're at {hotel_address}.\n\nNeed an airport transfer or anything else? Just reply or WhatsApp us.",
            'button' => 'View my booking',
        ],
        'arrival_day' => [
            'label' => 'Check-in day welcome', 'when' => 'Morning of arrival, from 07:00', 'to' => 'guest',
            'subject' => 'Welcome to {hotel} today',
            'body' => "Hi {guest},\n\nToday's the day! Your {room} will be ready from 14:00.\n\nAddress: {hotel_address}\nPhone / WhatsApp: {hotel_phone}\n\nSafe travels — see you soon.",
            'button' => 'View my booking',
        ],
        'review_request' => [
            'label' => 'Rate your stay', 'when' => 'Day after check-out, from 10:00', 'to' => 'guest',
            'subject' => 'How was your stay at {hotel}?',
            'body' => "Hi {guest},\n\nThank you for staying with us. We'd love to hear how it went — it takes a minute and helps other guests choose.",
            'button' => 'Rate your stay',
        ],
        'staff_new_booking' => [
            'label' => 'New booking (to the hotel)', 'when' => 'Every new booking / payment', 'to' => 'hotel',
            'subject' => 'New booking #{code} — {guest}',
            'body' => "New booking from {guest} ({guest_email}, {guest_phone}).\n\n{summary}\n\nPayment: {payment}.",
            'button' => 'Open in Admin',
        ],
    ];

    /** Saved settings merged over the defaults. */
    public static function get(string $key): array
    {
        $default = self::TEMPLATES[$key];
        $saved = (SiteSetting::current()->messages ?? [])[$key] ?? [];

        return [
            'enabled' => (bool) ($saved['enabled'] ?? true),
            'subject' => filled($saved['subject'] ?? null) ? $saved['subject'] : $default['subject'],
            'body'    => filled($saved['body'] ?? null) ? $saved['body'] : $default['body'],
        ] + $default;
    }

    public static function enabled(string $key): bool
    {
        return self::get($key)['enabled'];
    }

    /** Placeholder values for a booking. */
    public static function values(Booking $b): array
    {
        $site = SiteSetting::current();
        $money = fn ($v) => $site->formatMoney((float) $v, 2);
        $in = Carbon::parse($b->check_in);
        $out = Carbon::parse($b->check_out);
        $room = $b->room->type->name ?? 'Room';
        $payment = (int) $b->payment_status === 1 ? 'Paid'
            : ($b->payment_method === 'COD' ? 'Cash when you arrive' : 'Awaiting ' . $b->payment_method . ' payment');

        return [
            'guest' => trim(strtok((string) $b->name, ' ')) ?: 'Guest',
            'guest_name' => $b->name, 'guest_email' => $b->email, 'guest_phone' => $b->phone,
            'hotel' => $site->name, 'hotel_phone' => $site->phone ?: '', 'hotel_email' => $site->email ?: '', 'hotel_address' => $site->address ?: '',
            'room' => $room, 'code' => $b->code,
            'check_in' => $in->format('D d M Y'), 'check_out' => $out->format('D d M Y'),
            'nights' => (string) $b->total_night, 'rooms' => (string) $b->number_of_rooms, 'guests' => (string) $b->persion,
            'total' => $money($b->total_price), 'payment' => $payment,
            'summary' => "{$room} × {$b->number_of_rooms}\nCheck-in: {$in->format('D d M Y')}\nCheck-out: {$out->format('D d M Y')} ({$b->total_night} night" . ($b->total_night == 1 ? '' : 's') . ")\nGuests: {$b->persion}\nTotal: " . $money($b->total_price),
        ];
    }

    public static function fill(string $text, array $values): string
    {
        return preg_replace_callback('/\{([a-z_]+)\}/', fn ($m) => $values[$m[1]] ?? $m[0], $text);
    }

    /** Where the email's button goes. */
    public static function link(string $key, Booking $b): string
    {
        return match ($key) {
            'review_request' => route('user.review.create', $b->id),
            'booking_cancelled' => route('froom.all'),
            'staff_new_booking' => route('edit_booking', $b->id),
            default => route('user.booking'),
        };
    }
}
