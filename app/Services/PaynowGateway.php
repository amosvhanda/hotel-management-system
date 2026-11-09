<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Paynow\Payments\Paynow;

/**
 * Thin wrapper around the Paynow SDK so the controllers stay readable.
 * Credentials live in .env: PAYNOW_INTEGRATION_ID / PAYNOW_INTEGRATION_KEY.
 */
class PaynowGateway
{
    public function configured(): bool
    {
        return filled(config('services.paynow.integration_id')) && filled(config('services.paynow.integration_key'));
    }

    protected function client(Booking $booking): Paynow
    {
        return new Paynow(
            config('services.paynow.integration_id'),
            config('services.paynow.integration_key'),
            route('paynow.return', $booking->id), // where the customer comes back to
            route('paynow.result')                // where Paynow posts status updates
        );
    }

    /**
     * Start a payment. With a phone number it uses EcoCash express checkout,
     * otherwise it returns a Paynow web page to redirect the customer to.
     *
     * @return array{ok: bool, redirect: ?string, error: ?string}
     */
    public function start(Booking $booking, ?string $phone = null, string $method = 'ecocash'): array
    {
        try {
            $paynow = $this->client($booking);
            $payment = $paynow->createPayment($this->reference($booking), config('services.paynow.auth_email') ?: $booking->email);
            $payment->add('Room booking #' . $booking->code, (float) $booking->total_price);

            if ($phone) {
                $response = $paynow->sendMobile($payment, $this->localNumber($phone), 'ecocash');
            } else {
                $response = $paynow->send($payment);
            }

            if (! $response->success()) {
                $data = $response->data();

                return ['ok' => false, 'redirect' => null, 'error' => $data['error'] ?? 'Unknown error from Paynow.'];
            }

            $booking->update([
                'paynow_poll_url' => $response->pollUrl(),
                'paynow_status'   => 'Sent',
            ]);

            return ['ok' => true, 'redirect' => $phone ? null : $response->redirectUrl(), 'error' => null];
        } catch (\Throwable $e) {
            Log::error('Paynow init failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);

            return ['ok' => false, 'redirect' => null, 'error' => 'Could not reach Paynow. Please try again.'];
        }
    }

    /**
     * Ask Paynow for the latest status and update the booking. Returns the Paynow status text.
     */
    public function refresh(Booking $booking): ?string
    {
        if (! $booking->paynow_poll_url || ! $this->configured()) {
            return $booking->paynow_status;
        }

        try {
            $status = $this->client($booking)->pollTransaction($booking->paynow_poll_url);
            $this->apply($booking, $status->status(), $status->paid(), $status->paynowReference(), $status->amount());
        } catch (\Throwable $e) {
            Log::warning('Paynow poll failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
        }

        return $booking->fresh()->paynow_status;
    }

    /**
     * Handle Paynow's server-to-server result post (hash is verified by the SDK).
     */
    public function handleResult(array $post): ?Booking
    {
        $booking = Booking::where('code', $this->codeFromReference($post['reference'] ?? ''))->first();

        if (! $booking) {
            return null;
        }

        // Reject anything not signed with our integration key (forged or tampered posts).
        if (! isset($post['hash']) || ! \Paynow\Util\Hash::verify($post, strtolower((string) config('services.paynow.integration_key')))) {
            throw new \Paynow\Payments\HashMismatchException();
        }

        $status = new \Paynow\Core\StatusResponse($post);
        $this->apply($booking, $status->status(), $status->paid(), $status->paynowReference(), $status->amount());

        return $booking->fresh();
    }

    protected function apply(Booking $booking, ?string $status, bool $paid, $paynowRef, $amount): void
    {
        $alreadyPaid = (int) $booking->payment_status === 1;

        if ($paid && (float) $amount + 0.001 < (float) $booking->total_price) {
            Log::warning('Paynow amount mismatch', ['booking' => $booking->id, 'amount' => $amount]);
            $paid = false;
        }

        $booking->update([
            'paynow_status'  => $status,
            'payment_status' => $paid ? 1 : $booking->payment_status,
            'transation_id'  => $paynowRef ?: $booking->transation_id,
        ]);

        if ($paid && ! $alreadyPaid) {
            app(\App\Http\Controllers\Frontend\BookingController::class)->notifyAdmins($booking);
            app(\App\Services\GuestMessenger::class)->newBooking($booking->fresh('room.type'), 'payment_received');
        }

        // Cancelled/failed payments release the room dates.
        if (! $paid && in_array(strtolower((string) $status), ['cancelled', 'failed'], true) && ! $booking->isCancelled()) {
            $booking->cancelAndRelease();
            app(\App\Services\GuestMessenger::class)->send($booking, 'booking_cancelled');
        }
    }

    /** 263771234567 / +263771234567 / 771234567 → 0771234567 */
    protected function localNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        $digits = preg_replace('/^263/', '', $digits);

        return '0' . ltrim($digits, '0');
    }

    protected function reference(Booking $booking): string
    {
        return 'Booking ' . $booking->code;
    }

    protected function codeFromReference(string $reference): string
    {
        return trim(str_replace('Booking', '', $reference));
    }
}
