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
        $this->post('/checkout/store/', $this->guestDetails(['payment_method' => 'onemoney', 'paynow_phone' => '0712345678']))
            ->assertSessionHasErrors('payment_method'); // OneMoney is no longer offered
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

    /* ---------- Regression tests for the end-to-end booking fixes ---------- */

    public function test_booking_sheet_sends_plain_numbers_the_server_accepts(): void
    {
        $room = $this->makeRoom(2);
        $html = $this->get('/search/room/details/' . $room->id . '?check_in=' . $this->date(10) . '&check_out=' . $this->date(12) . '&persion=2')
            ->assertOk()->getContent();

        // "01"/"02" values used to fail Laravel's integer rule, so no booking could ever be made.
        $this->assertStringNotContainsString('value="01"', $html);
        $this->assertStringNotContainsString('value="02"', $html);
        $this->assertMatchesRegularExpression('/<option value="2"\s+selected/', $html, 'The searched guest count is preselected.');
    }

    public function test_signed_out_guest_keeps_the_room_through_login_and_lands_on_checkout(): void
    {
        $room = $this->makeRoom(2);
        $guest = User::factory()->create(['password' => bcrypt('secret-pass-123')]);

        $this->post('/booking/store/', [
            'room_id' => $room->id, 'check_in' => $this->date(10), 'check_out' => $this->date(12),
            'persion' => 2, 'number_of_rooms' => 1,
        ])->assertRedirect(route('login'));

        $this->post('/login', ['login' => $guest->email, 'password' => 'secret-pass-123'])
            ->assertRedirect(route('checkout'));
        $this->get('/checkout')->assertOk()->assertSee('Complete your booking');
    }

    public function test_new_guest_who_registers_mid_booking_lands_on_checkout(): void
    {
        $room = $this->makeRoom(2);
        $this->post('/booking/store/', [
            'room_id' => $room->id, 'check_in' => $this->date(10), 'check_out' => $this->date(12),
            'persion' => 1, 'number_of_rooms' => 1,
        ]);

        $this->post('/register', [
            'name' => 'Test Guest', 'email' => 'new.guest@example.com',
            'password' => 'NewGuest!2345', 'password_confirmation' => 'NewGuest!2345',
        ])->assertRedirect(route('checkout'));
    }

    public function test_more_guests_than_the_rooms_can_sleep_is_refused(): void
    {
        $guest = User::factory()->create();
        $room = $this->makeRoom(3); // sleeps 2 per room

        $this->actingAs($guest)->post('/booking/store/', [
            'room_id' => $room->id, 'check_in' => $this->date(10), 'check_out' => $this->date(12),
            'persion' => 3, 'number_of_rooms' => 1,
        ])->assertSessionHas('alert-type', 'error');

        $this->post('/booking/store/', [
            'room_id' => $room->id, 'check_in' => $this->date(10), 'check_out' => $this->date(12),
            'persion' => 3, 'number_of_rooms' => 2,
        ])->assertRedirect(route('checkout'));
    }

    public function test_guest_can_cancel_an_unpaid_upcoming_booking_and_the_room_is_freed(): void
    {
        $guest = User::factory()->create();
        $room = $this->makeRoom(1);
        $booking = $this->makeBooking($room, $guest, $this->date(10), $this->date(12));
        $this->assertSame(0, Booking::availableRooms($room->id, $this->date(10), $this->date(12)));

        $this->actingAs($guest)->post(route('user.booking.cancel', $booking->id))->assertSessionHas('alert-type', 'success');

        $this->assertSame(Booking::STATUS_CANCELLED, (int) $booking->fresh()->status);
        $this->assertSame(1, Booking::availableRooms($room->id, $this->date(10), $this->date(12)));
    }

    public function test_guest_cannot_cancel_someone_elses_or_a_paid_booking(): void
    {
        $room = $this->makeRoom(2);
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $theirs = $this->makeBooking($room, $owner, $this->date(10), $this->date(12));
        $paid = $this->makeBooking($room, $other, $this->date(10), $this->date(12), ['payment_status' => 1]);

        $this->actingAs($other)->post(route('user.booking.cancel', $theirs->id))->assertNotFound();
        $this->post(route('user.booking.cancel', $paid->id))->assertSessionHas('alert-type', 'error');
        $this->assertSame(Booking::STATUS_PENDING, (int) $paid->fresh()->status);
    }

    public function test_admin_cancel_frees_the_room_and_reopening_blocks_it_again(): void
    {
        $admin = $this->superAdmin();
        $room = $this->makeRoom(1);
        $booking = $this->makeBooking($room, User::factory()->create(), $this->date(10), $this->date(12));

        $this->actingAs($admin)->post(route('update.booking.status', $booking->id), ['payment_status' => 0, 'status' => 2]);
        $this->assertTrue($booking->fresh()->isCancelled());
        $this->assertSame(1, Booking::availableRooms($room->id, $this->date(10), $this->date(12)));

        $this->post(route('update.booking.status', $booking->id), ['payment_status' => 0, 'status' => 0]);
        $this->assertSame(0, Booking::availableRooms($room->id, $this->date(10), $this->date(12)));
    }

    public function test_mobile_money_options_are_hidden_until_paynow_is_configured(): void
    {
        config(['services.paynow.integration_id' => null, 'services.paynow.integration_key' => null]);
        $guest = User::factory()->create();
        $this->startBooking($guest, $this->makeRoom(2));

        $this->get('/checkout')->assertOk()
            ->assertDontSee('value="ecocash"', false)
            ->assertSee('value="COD"', false);
    }

    /* ---------- Payment options: Paynow, EcoCash, Cash ---------- */

    private function fakeGateway(?string $redirect): object
    {
        $fake = new class($redirect) extends \App\Services\PaynowGateway {
            public array $calls = [];
            public function __construct(private ?string $redirect) {}
            public function configured(): bool { return true; }
            public function start(\App\Models\Booking $booking, ?string $phone = null, string $method = 'ecocash'): array
            {
                $this->calls[] = compact('phone', 'method');
                return ['ok' => true, 'redirect' => $phone ? null : $this->redirect, 'error' => null];
            }
            public function refresh(\App\Models\Booking $booking): ?string { return 'Sent'; }
        };
        $this->app->instance(\App\Services\PaynowGateway::class, $fake);

        return $fake;
    }

    public function test_checkout_offers_paynow_ecocash_and_cash(): void
    {
        $this->fakeGateway('https://www.paynow.co.zw/payment/confirm/1');
        $this->startBooking(User::factory()->create(), $this->makeRoom(2));

        $this->get('/checkout')->assertOk()
            ->assertSee('value="paynow"', false)
            ->assertSee('value="ecocash"', false)
            ->assertSee('value="COD"', false)
            ->assertDontSee('OneMoney');
    }

    public function test_paynow_option_sends_the_guest_to_the_paynow_page(): void
    {
        $fake = $this->fakeGateway('https://www.paynow.co.zw/payment/confirm/abc');
        $this->startBooking(User::factory()->create(), $this->makeRoom(2));

        $this->post('/checkout/store/', $this->guestDetails(['payment_method' => 'paynow']))
            ->assertRedirect('https://www.paynow.co.zw/payment/confirm/abc');

        $this->assertSame('Paynow', Booking::first()->payment_method);
        $this->assertNull($fake->calls[0]['phone'], 'Paynow uses the web page, not a phone prompt.');
    }

    public function test_ecocash_option_sends_a_prompt_to_the_phone(): void
    {
        $fake = $this->fakeGateway(null);
        $this->startBooking(User::factory()->create(), $this->makeRoom(2));

        $this->post('/checkout/store/', $this->guestDetails(['payment_method' => 'ecocash', 'paynow_phone' => '0771234567']))
            ->assertRedirect(route('paynow.return', Booking::first()->id));

        $this->assertSame('EcoCash', Booking::first()->payment_method);
        $this->assertSame('0771234567', $fake->calls[0]['phone']);
    }

    public function test_cash_booking_is_paid_on_arrival(): void
    {
        $this->startBooking(User::factory()->create(), $this->makeRoom(2));

        $this->post('/checkout/store/', $this->guestDetails(['payment_method' => 'COD']))
            ->assertRedirect(route('user.booking'));
        $this->get('/user/booking')->assertSee('Cash on arrival');
    }
}
