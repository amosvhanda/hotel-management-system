<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingComplete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    private function startBooking(User $user, $room, int $rooms = 1, int $from = 10, int $to = 12)
    {
        return $this->actingAs($user)->post('/booking/store/', [
            'room_id' => $room->id, 'check_in' => $this->date($from), 'check_out' => $this->date($to),
            'persion' => 2, 'number_of_rooms' => $rooms,
        ]);
    }

    private function guestDetails(array $extra = []): array
    {
        return array_merge([
            'name' => 'Farai Mutasa', 'email' => 'farai@example.com', 'phone' => '0771234567',
            'country' => 'Zimbabwe', 'state' => 'Harare', 'address' => 'Borrowdale', 'payment_method' => 'COD',
        ], $extra);
    }

    public function test_search_shows_available_rooms(): void
    {
        $this->makeRoom(2, ['type' => 'Executive Suite']);

        $this->get('/bookings?check_in=' . $this->date(10) . '&check_out=' . $this->date(12) . '&persion=1')
            ->assertOk()->assertSee('Executive Suite');
    }

    public function test_search_rejects_past_dates_and_reversed_dates(): void
    {
        $this->from('/')->get('/bookings?check_in=' . $this->date(-3) . '&check_out=' . $this->date(-1))->assertSessionHasErrors('check_in');
        $this->from('/')->get('/bookings?check_in=' . $this->date(5) . '&check_out=' . $this->date(3))->assertSessionHasErrors('check_out');
    }

    public function test_guest_must_sign_in_before_checkout(): void
    {
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_pay_at_hotel_booking_is_saved_with_correct_price_and_admins_are_notified(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $guest = User::factory()->create();
        $room = $this->makeRoom(2); // $100/night, 10% discount

        $this->startBooking($guest, $room, 1, 10, 12)->assertRedirect(route('checkout'));
        $this->get('/checkout')->assertOk()->assertSee('Complete your booking');

        $this->post('/checkout/store/', $this->guestDetails())->assertRedirect(route('user.booking'));

        $booking = Booking::sole();
        $this->assertSame($guest->id, (int) $booking->user_id);
        $this->assertEquals(200, $booking->subtotal);
        $this->assertEquals(20, $booking->discount);
        $this->assertEquals(180, $booking->total_price);
        $this->assertSame(2, $booking->bookedDates()->count()); // two nights blocked
        Notification::assertSentTo($admin, BookingComplete::class);
    }

    public function test_cannot_book_more_rooms_than_are_free(): void
    {
        $room = $this->makeRoom(1);
        $this->makeBooking($room, User::factory()->create(), $this->date(10), $this->date(12));

        $this->startBooking(User::factory()->create(), $room)->assertSessionHas('alert-type', 'error');
        $this->assertSame(1, Booking::count());
    }

    public function test_room_taken_between_selection_and_payment_is_not_double_booked(): void
    {
        $room = $this->makeRoom(1);
        $guest = User::factory()->create();
        $this->startBooking($guest, $room);

        // Someone else books the last room meanwhile.
        $this->makeBooking($room, User::factory()->create(), $this->date(10), $this->date(12));

        $this->actingAs($guest)->post('/checkout/store/', $this->guestDetails())->assertSessionHas('alert-type', 'error');
        $this->assertSame(1, Booking::count());
    }

    public function test_mobile_money_number_must_match_the_network(): void
    {
        $guest = User::factory()->create();
        $this->startBooking($guest, $this->makeRoom(2));

        $this->post('/checkout/store/', $this->guestDetails(['payment_method' => 'ecocash', 'paynow_phone' => '0712345678']))
            ->assertSessionHasErrors('paynow_phone');
        $this->post('/checkout/store/', $this->guestDetails(['payment_method' => 'onemoney', 'paynow_phone' => '0771234567']))
            ->assertSessionHasErrors('paynow_phone');
        $this->assertSame(0, Booking::count());
    }

    public function test_switching_back_to_pay_at_hotel_ignores_a_leftover_phone_number(): void
    {
        $guest = User::factory()->create();
        $this->startBooking($guest, $this->makeRoom(2));

        $this->post('/checkout/store/', $this->guestDetails(['payment_method' => 'COD', 'paynow_phone' => '0712345678']))
            ->assertSessionHasNoErrors();
        $this->assertSame(1, Booking::count());
    }

    public function test_guest_sees_own_bookings_and_downloads_own_invoice(): void
    {
        $guest = User::factory()->create();
        $booking = $this->makeBooking($this->makeRoom(1), $guest, $this->date(10), $this->date(12));

        $this->actingAs($guest)->get('/user/booking')->assertOk()->assertSee('#' . $booking->code);
        $this->get('/user/invoice/' . $booking->id)->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
