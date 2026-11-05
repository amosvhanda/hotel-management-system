<?php

namespace App\Mail;

use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookConfirm extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array $data booking details (see BookingController::UpdateBookingStatus) */
    public function __construct(private array $data)
    {
    }

    public function envelope(): Envelope
    {
        $code = $this->data['code'] ?? null;

        return new Envelope(
            subject: 'Your booking is confirmed' . ($code ? ' — #' . $code : '') . ' | ' . SiteSetting::current()->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking_mail',
            with: ['booking' => $this->data, 'site' => SiteSetting::current()],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
