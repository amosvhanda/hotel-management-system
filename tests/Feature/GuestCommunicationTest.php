<?php

namespace Tests\Feature;

use App\Mail\GuestMessageMail;
use App\Models\Booking;
use App\Models\MessageLog;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class GuestCommunicationTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        SiteSetting::create(['site_name' => 'Mopane Heights Hotel', 'email' => 'reservations@hotel.test', 'phone' => '0771 234 567', 'address' => '45 Samora Machel Ave']);
        SiteSetting::flushCurrent();
    }

    private function sentWith(string $subjectPart, ?string $to = null): void
    {
        Mail::assertSent(GuestMessageMail::class, fn ($m) => str_contains($m->subjectLine, $subjectPart) && (! $to || $m->hasTo($to)));
    }

    private function startBooking(User $user, $room)
    {
        return $this->actingAs($user)->post('/booking/store/', ['room_id' => $room->id, 'check_in' => $this->date(10), 'check_out' => $this->date(12), 'persion' => 2, 'number_of_rooms' => 1]);
    }

    public function test_cash_booking_emails_guest_and_hotel(): void
    {
        $guest = User::factory()->create();
        $this->startBooking($guest, $this->makeRoom(2, ['type' => 'Executive Suite']));
        $this->post('/checkout/store/', ['name' => 'Farai Mutasa', 'email' => 'farai@guest.test', 'phone' => '0771234567', 'country' => 'Zimbabwe',
            'state' => 'Harare', 'address' => 'Borrowdale', 'payment_method' => 'COD']);

        $this->sentWith('received', 'farai@guest.test');
        $this->sentWith('New booking', 'reservations@hotel.test');
        Mail::assertSent(GuestMessageMail::class, fn ($m) => str_contains($m->bodyText, 'Executive Suite') && str_contains($m->bodyText, 'Hi Farai'));
        $this->assertSame(2, MessageLog::where('status', 'sent')->count());
    }

    public function test_cancelling_emails_the_guest(): void
    {
        $guest = User::factory()->create();
        $booking = $this->makeBooking($this->makeRoom(1), $guest, $this->date(10), $this->date(12));

        $this->actingAs($guest)->post(route('user.booking.cancel', $booking->id));
        $this->sentWith('cancelled', $booking->email);
    }

    public function test_reminders_go_out_once_at_the_right_time(): void
    {
        $room = $this->makeRoom(3);
        $guest = User::factory()->create();
        $in2days = $this->makeBooking($room, $guest, $this->date(2), $this->date(4), ['email' => 'soon@guest.test']);
        $today = $this->makeBooking($room, $guest, $this->date(0), $this->date(2), ['email' => 'today@guest.test']);
        $left = $this->makeBooking($room, $guest, $this->date(-3), $this->date(-1), ['email' => 'left@guest.test']);
        $cancelled = $this->makeBooking($room, $guest, $this->date(2), $this->date(3), ['email' => 'no@guest.test', 'status' => Booking::STATUS_CANCELLED]);

        $this->travelTo(now()->setTime(6, 0));
        $this->artisan('guests:send-messages')->assertSuccessful();
        Mail::assertNothingSent(); // too early for all of them

        $this->travelTo(now()->setTime(10, 30));
        $this->artisan('guests:send-messages');
        $this->artisan('guests:send-messages'); // second run sends nothing new

        $this->sentWith('See you in 2 days', 'soon@guest.test');
        $this->sentWith('Welcome to', 'today@guest.test');
        $this->sentWith('How was your stay', 'left@guest.test');
        Mail::assertNotSent(GuestMessageMail::class, fn ($m) => $m->hasTo('no@guest.test'));
        Mail::assertSent(GuestMessageMail::class, 3);
    }

    public function test_no_review_request_if_already_reviewed(): void
    {
        $guest = User::factory()->create();
        $b = $this->makeBooking($this->makeRoom(1), $guest, $this->date(-3), $this->date(-1));
        Review::create(['booking_id' => $b->id, 'user_id' => $guest->id, 'rooms_id' => $b->rooms_id, 'rating' => 5, 'comment' => 'Great stay overall']);

        $this->travelTo(now()->setTime(11, 0));
        $this->artisan('guests:send-messages');
        Mail::assertNothingSent();
    }

    public function test_staff_can_switch_off_and_reword_messages(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->get(route('guest.messages'))->assertOk()->assertSee('Pre-arrival reminder');

        $this->post(route('guest.messages.update'), [
            'whatsapp_number' => '0772 000 111',
            'messages' => [
                'pre_arrival' => ['enabled' => '1', 'subject' => 'Almost time, {guest}!', 'body' => 'Room {room} awaits.'],
                // arrival_day left unchecked → switched off
            ],
        ])->assertSessionHas('alert-type', 'success');

        $guest = User::factory()->create();
        $room = $this->makeRoom(2, ['type' => 'Twin Room']);
        $this->makeBooking($room, $guest, $this->date(2), $this->date(3), ['name' => 'Nyasha Chirwa', 'email' => 'n@guest.test']);
        $this->makeBooking($room, $guest, $this->date(0), $this->date(1), ['email' => 'off@guest.test']);

        $this->travelTo(now()->setTime(12, 0));
        $this->artisan('guests:send-messages');

        Mail::assertSent(GuestMessageMail::class, fn ($m) => $m->subjectLine === 'Almost time, Nyasha!' && $m->bodyText === 'Room Twin Room awaits.');
        Mail::assertNotSent(GuestMessageMail::class, fn ($m) => $m->hasTo('off@guest.test'));
        $this->assertSame('263772000111', SiteSetting::current()->whatsappDigits());
    }

    public function test_send_test_email_goes_to_the_staff_member(): void
    {
        $admin = $this->superAdmin();
        $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));

        $this->actingAs($admin)->post(route('guest.messages.test', 'pre_arrival'))->assertSessionHas('alert-type', 'success');
        Mail::assertSent(GuestMessageMail::class, fn ($m) => $m->hasTo($admin->email) && str_starts_with($m->subjectLine, '[TEST]'));
    }

    public function test_whatsapp_button_on_site_and_booking(): void
    {
        $admin = $this->superAdmin();
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7), ['phone' => '0782 345 678']);

        $this->get('/')->assertOk()->assertSee('https://wa.me/263771234567', false)->assertSee('Chat on WhatsApp');
        $this->actingAs($admin)->get(route('edit_booking', $booking->id))->assertSee('https://wa.me/263782345678', false);
    }

    public function test_sitemap_robots_and_link_previews(): void
    {
        $room = $this->makeRoom(1, ['type' => 'Executive Suite', 'image' => 'suite.jpg', 'short_desc' => 'Our biggest suite.']);

        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('room.details', $room->id), false);
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: ' . url('/sitemap.xml'))->assertSee('Disallow: /admin');

        $this->get('/room/details/' . $room->id)->assertOk()
            ->assertSee('<meta property="og:title" content="Executive Suite | Mopane Heights Hotel">', false)
            ->assertSee('upload/roomimg/suite.jpg', false)
            ->assertSee('"@type":"HotelRoom"', false);
        $this->get('/')->assertSee('"@type":"Hotel"', false)->assertSee('og:image', false);
    }
}
