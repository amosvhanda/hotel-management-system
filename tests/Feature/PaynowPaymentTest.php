<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingComplete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Paynow\Util\Hash;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

/** Paynow (EcoCash / OneMoney) result callbacks. Keys come from phpunit.xml. */
class PaynowPaymentTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    private function pendingMobileBooking(): Booking
    {
        return $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(10), $this->date(12), [
            'payment_method' => 'EcoCash', 'paynow_status' => 'Sent', 'paynow_poll_url' => 'https://example.invalid/poll',
        ]);
    }

    private function signed(Booking $b, string $status, ?string $amount = null): array
    {
        $post = [
            'reference' => 'Booking ' . $b->code, 'paynowreference' => 'PN-12345',
            'amount' => $amount ?? number_format((float) $b->total_price, 2, '.', ''),
            'status' => $status, 'pollurl' => 'https://example.invalid/poll',
        ];
        $post['hash'] = Hash::make($post, strtolower(config('services.paynow.integration_key')));

        return $post;
    }

    public function test_paid_callback_marks_booking_paid_and_notifies_admins(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $booking = $this->pendingMobileBooking();

        $this->post('/paynow/result', $this->signed($booking, 'Paid'))->assertOk();

        $booking->refresh();
        $this->assertEquals(1, $booking->payment_status);
        $this->assertSame('PN-12345', $booking->transation_id);
        Notification::assertSentTo($admin, BookingComplete::class);
    }

    public function test_forged_callback_is_ignored(): void
    {
        $booking = $this->pendingMobileBooking();
        $post = $this->signed($booking, 'Paid');
        $post['hash'] = 'FORGED';

        $this->post('/paynow/result', $post)->assertOk(); // always 200 so Paynow stops retrying
        $this->assertEquals(0, $booking->refresh()->payment_status);
    }

    public function test_underpayment_is_not_marked_paid(): void
    {
        $booking = $this->pendingMobileBooking();

        $this->post('/paynow/result', $this->signed($booking, 'Paid', '1.00'));
        $this->assertEquals(0, $booking->refresh()->payment_status);
    }

    public function test_cancelled_payment_releases_the_room_dates(): void
    {
        $booking = $this->pendingMobileBooking();
        $this->assertSame(2, $booking->bookedDates()->count());

        $this->post('/paynow/result', $this->signed($booking, 'Cancelled'));

        $this->assertSame(0, $booking->bookedDates()->count());
        $this->assertSame(1, Booking::availableRooms($booking->rooms_id, $this->date(10), $this->date(12)));
    }

    public function test_result_url_does_not_need_a_csrf_token_or_login(): void
    {
        $this->post('/paynow/result', ['reference' => 'Booking 000'])->assertOk();
    }

    public function test_guest_sees_payment_received_page(): void
    {
        $booking = $this->pendingMobileBooking();
        $booking->update(['payment_status' => 1, 'paynow_status' => 'paid']);

        $this->actingAs($booking->user)->get('/paynow/return/' . $booking->id)->assertOk()->assertSee('Payment received');
    }
}
