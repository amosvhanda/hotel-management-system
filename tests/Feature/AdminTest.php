<?php

namespace Tests\Feature;

use App\Mail\GuestMessageMail;
use App\Models\Booking;
use App\Models\BookingRoomList;
use App\Models\Faq;
use App\Models\RoomNumber;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    public function test_admin_pages_load(): void
    {
        $admin = $this->superAdmin();
        $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(0), $this->date(2));

        foreach (['/admin/dashboard', '/booking/list', '/edit_booking/1', '/all/service', '/add/service', '/site/setting',
                  '/homepage/setting', '/all/faq', '/add/faq',
                  '/smtp/setting', '/all/roles', '/all/permission', '/all/admin', '/booking/report/', '/view/room/list'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_dashboard_shows_real_numbers(): void
    {
        $admin = $this->superAdmin();
        $this->makeBooking($this->makeRoom(2), User::factory()->create(), $this->date(0), $this->date(2), ['payment_status' => 1, 'total_price' => 180]);

        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()->assertSee('Arrivals today')->assertSee('50%')->assertSee('$180.00');
    }

    public function test_confirming_a_booking_emails_the_guest_once(): void
    {
        Mail::fake();
        $admin = $this->superAdmin();
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));

        $this->actingAs($admin)->post('/update/booking/status/' . $booking->id, ['payment_status' => 1, 'status' => 1])
            ->assertRedirect()->assertSessionHas('alert-type', 'success');
        $this->actingAs($admin)->post('/update/booking/status/' . $booking->id, ['payment_status' => 1, 'status' => 1]);

        $this->assertEquals(1, $booking->refresh()->status);
        Mail::assertSent(GuestMessageMail::class, fn ($m) => str_contains($m->subjectLine, "is confirmed") && $m->hasTo($booking->email));
        Mail::assertSent(GuestMessageMail::class, 1);
    }

    public function test_email_failure_does_not_break_the_update(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));
        $admin = $this->superAdmin();
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));

        $this->actingAs($admin)->post('/update/booking/status/' . $booking->id, ['payment_status' => 0, 'status' => 1])
            ->assertSessionHas('alert-type', 'warning');
        $this->assertEquals(1, $booking->refresh()->status);
    }

    public function test_assign_room_respects_number_of_rooms(): void
    {
        $admin = $this->superAdmin();
        $room = $this->makeRoom(2);
        $booking = $this->makeBooking($room, User::factory()->create(), $this->date(5), $this->date(7));
        [$first, $second] = RoomNumber::where('rooms_id', $room->id)->pluck('id')->all();

        $this->actingAs($admin)->get("/assign_room/store/{$booking->id}/{$first}")->assertSessionHas('alert-type', 'success');
        $this->actingAs($admin)->get("/assign_room/store/{$booking->id}/{$second}")->assertSessionHas('alert-type', 'error');
        $this->assertSame(1, BookingRoomList::count());
    }

    public function test_editing_dates_recalculates_price_and_checks_availability(): void
    {
        $admin = $this->superAdmin();
        $room = $this->makeRoom(1); // $100/night, 10% off
        $booking = $this->makeBooking($room, User::factory()->create(), $this->date(5), $this->date(7));

        $this->actingAs($admin)->post('/update/booking/' . $booking->id, [
            'check_in' => $this->date(5), 'check_out' => $this->date(8), 'number_of_rooms' => 1,
        ])->assertSessionHas('alert-type', 'success');

        $booking->refresh();
        $this->assertEquals(3, $booking->total_night);
        $this->assertEquals(270, $booking->total_price);
        $this->assertSame(3, $booking->bookedDates()->count());
    }

    public function test_services_can_be_created_updated_and_deleted(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)->post('/service/store', ['title' => 'Airport Transfers', 'description' => 'Pickup from the airport.'])
            ->assertRedirect(route('all.service'));
        $service = Service::sole();

        $this->actingAs($admin)->put('/service/update/' . $service->id, ['title' => 'Airport Shuttle', 'description' => 'Daily shuttle.'])
            ->assertRedirect(route('all.service'));
        $this->assertSame('Airport Shuttle', $service->refresh()->title);
        $this->get('/service/details/' . $service->id)->assertOk()->assertSee('Airport Shuttle');

        $this->actingAs($admin)->get('/delete/service/' . $service->id);
        $this->assertSame(0, Service::count());
    }

    public function test_site_settings_keep_the_fields_the_form_shows(): void
    {
        $admin = $this->superAdmin();
        $site = SiteSetting::create(['site_name' => 'Test Hotel', 'social_sharing_enabled' => true]);

        $this->actingAs($admin)->post(route('site.update'), [
            'id' => $site->id,
            'site_description' => 'A Harare stay',
            'default_currency' => 'USD',
            'google_analytics' => 'G-TEST',
            'maintenance_mode' => '1',
        ])->assertRedirect();

        $site->refresh();
        $this->assertSame('A Harare stay', $site->site_description);
        $this->assertSame('USD', $site->default_currency);
        $this->assertSame('G-TEST', $site->google_analytics);
        $this->assertTrue($site->maintenance_mode);
        $this->assertFalse($site->social_sharing_enabled);
    }

    public function test_homepage_headline_and_faq_are_editable(): void
    {
        $admin = $this->superAdmin();
        $site = SiteSetting::create([
            'site_name' => 'Mopane Heights Hotel',
            'default_currency' => 'USD',
        ]);

        $this->actingAs($admin)->post(route('homepage.update'), [
            'id' => $site->id,
            'hero_title' => 'Evenings on Samora Machel',
            'hero_text' => 'A calm city hotel.',
        ])->assertRedirect();

        SiteSetting::flushCurrent();
        $this->get('/')->assertOk()->assertSee('Evenings on Samora Machel');

        $this->actingAs($admin)->post(route('faq.store'), [
            'question' => 'Is breakfast included?',
            'answer' => 'Breakfast is served from half past six.',
            'sort_order' => 1,
        ])->assertRedirect();

        $this->assertSame(1, Faq::count());
        SiteSetting::flushCurrent();
        $this->get('/')->assertSee('Is breakfast included?')->assertSee('Breakfast is served from half past six.');
    }

    public function test_staff_without_service_permission_get_403(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $receptionist = User::factory()->admin()->create();
        $receptionist->assignRole('Receptionist');

        $this->actingAs($receptionist)->get('/all/service')->assertForbidden();
        $this->actingAs($receptionist)->get('/booking/list')->assertOk();
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->get('/delete/admin/' . $admin->id)->assertSessionHas('alert-type', 'error');
        $this->assertNotNull($admin->fresh());
    }
}
