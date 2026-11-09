<?php

namespace Tests\Feature;

use App\Http\Controllers\Frontend\BookingController;
use App\Models\Gallery;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

/** Covers the sign-in pages, brand colours, notifications, dashboard, gallery update, tables and forms. */
class PolishFeaturesTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    // ---------- Sign-in pages ----------

    public function test_guest_auth_pages_render_with_the_shared_layout(): void
    {
        $this->get('/login')->assertOk()->assertSee('Welcome back')->assertSee('hms-tokens.css', false);
        $this->get('/register')->assertOk()->assertSee('Create your account');
        $this->get('/forgot-password')->assertOk()->assertSee('Reset your password')->assertSee('Send reset link');
    }

    public function test_auth_pages_show_the_uploaded_logo(): void
    {
        SiteSetting::create(['site_name' => 'Mopane Heights', 'logo' => 'upload/site/logo.png']);

        $this->get('/login')->assertOk()->assertSee('upload/site/logo.png', false);
    }

    // ---------- Brand colours ----------

    public function test_brand_colours_page_loads_for_admin_and_not_for_guests(): void
    {
        $this->get('/theme/setting')->assertRedirect();
        $this->actingAs($this->superAdmin())->get('/theme/setting')->assertOk()->assertSee('Brand colours')->assertSee('Start from a palette');
    }

    public function test_saving_colours_changes_the_public_site_admin_and_login(): void
    {
        $admin = $this->superAdmin();
        SiteSetting::create(['site_name' => 'Mopane Heights']);

        $this->actingAs($admin)->post('/theme/update', $this->palette(['canopy' => '#112233']))
            ->assertRedirect()->assertSessionHas('alert-type', 'success');

        $this->actingAs($admin)->get('/admin/dashboard')->assertSee('--hms-canopy:#112233', false);

        auth()->logout();
        $this->get('/')->assertOk()->assertSee('--hms-canopy:#112233', false);
        $this->get('/login')->assertOk()->assertSee('--hms-canopy:#112233', false);
    }

    public function test_unchanged_palette_adds_no_override_block(): void
    {
        SiteSetting::create(['site_name' => 'Mopane Heights']);

        $this->get('/')->assertOk()->assertDontSee('id="hms-theme"', false);
    }

    public function test_invalid_colours_are_rejected(): void
    {
        $admin = $this->superAdmin();
        SiteSetting::create(['site_name' => 'Mopane Heights']);

        $this->actingAs($admin)->post('/theme/update', $this->palette(['canopy' => 'red; } body { display:none']))
            ->assertSessionHasErrors('canopy');

        $this->assertFalse(SiteSetting::current()->themeIsCustom());
    }

    public function test_reset_restores_the_original_palette(): void
    {
        $admin = $this->superAdmin();
        SiteSetting::create(['site_name' => 'Mopane Heights']);
        $this->actingAs($admin)->post('/theme/update', $this->palette(['leaf' => '#445566']));
        $this->assertTrue(SiteSetting::current()->themeIsCustom());

        $this->actingAs($admin)->post('/theme/reset')->assertRedirect('/theme/setting');

        $this->assertFalse(SiteSetting::current()->themeIsCustom());
        $this->get('/')->assertDontSee('id="hms-theme"', false);
    }

    public function test_theme_colours_only_accept_six_digit_hex(): void
    {
        $s = new SiteSetting();
        $s->theme = ['canopy' => '#abc', 'leaf' => '#3F6B4A', 'sand' => 'javascript:alert(1)'];

        $colours = $s->themeColors();

        $this->assertSame(SiteSetting::THEME_DEFAULTS['canopy'], $colours['canopy']);
        $this->assertSame('#3f6b4a', $colours['leaf']);
        $this->assertSame(SiteSetting::THEME_DEFAULTS['sand'], $colours['sand']);
    }

    // ---------- Notifications ----------

    public function test_a_new_booking_notifies_admins_with_a_useful_message(): void
    {
        $admin = $this->superAdmin();
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(3), $this->date(5), ['name' => 'Tendai Moyo']);

        app(BookingController::class)->notifyAdmins($booking);

        $n = $admin->unreadNotifications()->first();
        $this->assertNotNull($n);
        $this->assertStringContainsString('Tendai Moyo', $n->data['message']);
        $this->assertSame($booking->id, $n->data['booking_id']);
        $this->assertFalse($n->data['paid']);
    }

    public function test_the_bell_lists_notifications_and_opening_one_goes_to_the_booking_and_marks_it_read(): void
    {
        $admin = $this->superAdmin();
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(3), $this->date(5));
        app(BookingController::class)->notifyAdmins($booking);
        $n = $admin->unreadNotifications()->first();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()->assertSee('#' . $booking->code)->assertSee('See all notifications');

        $this->actingAs($admin)->get('/admin/notifications/' . $n->id . '/open')->assertRedirect('/edit_booking/' . $booking->id);
        $this->assertNotNull($n->fresh()->read_at);
        $this->assertSame(0, $admin->unreadNotifications()->count());
    }

    public function test_mark_all_as_read_and_the_notifications_page(): void
    {
        $admin = $this->superAdmin();
        $room = $this->makeRoom(2);
        foreach ([3, 6] as $d) {
            app(BookingController::class)->notifyAdmins($this->makeBooking($room, User::factory()->create(), $this->date($d), $this->date($d + 1)));
        }

        $this->actingAs($admin)->get('/admin/notifications')->assertOk()->assertSee('Mark all as read');
        $this->actingAs($admin)->get('/admin/notifications?filter=unread')->assertOk();

        $this->actingAs($admin)->post('/admin/notifications/read-all')->assertRedirect();
        $this->assertSame(0, $admin->unreadNotifications()->count());
        $this->actingAs($admin)->get('/admin/notifications?filter=unread')->assertSee('all caught up');
    }

    public function test_an_admin_cannot_open_someone_elses_notification(): void
    {
        $admin = $this->superAdmin();
        $other = User::factory()->admin()->create();
        app(BookingController::class)->notifyAdmins($this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(3), $this->date(4)));
        $theirs = $other->notifications()->first();

        $this->actingAs($admin)->get('/admin/notifications/' . $theirs->id . '/open')->assertNotFound();
    }

    public function test_notification_pages_need_an_admin(): void
    {
        $this->get('/admin/notifications')->assertRedirect();
        $this->actingAs(User::factory()->create())->get('/admin/notifications')->assertRedirect();
    }

    // ---------- Dashboard ----------

    public function test_dashboard_sections_render_with_and_without_data(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()
            ->assertSee('Needs your attention')->assertSee('Occupancy, next 14 nights')->assertSee('No bookings yet');

        $this->makeBooking($this->makeRoom(2), User::factory()->create(), $this->date(0), $this->date(2), ['payment_status' => 1]);

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()
            ->assertSee('Top room types')->assertSee('How guests pay')->assertSee('Cash on arrival');
    }

    public function test_the_dashboard_forecast_counts_rooms_for_each_night(): void
    {
        $admin = $this->superAdmin();
        $this->makeBooking($this->makeRoom(2), User::factory()->create(), $this->date(0), $this->date(2));

        $forecast = $this->actingAs($admin)->get('/admin/dashboard')->viewData('forecast');

        $this->assertCount(14, $forecast);
        $this->assertSame(1, $forecast[0]['taken']);
        $this->assertSame(1, $forecast[1]['taken']);
        $this->assertSame(0, $forecast[2]['taken']);
        $this->assertSame(50, $forecast[0]['pct']);
    }

    public function test_the_sidebar_shows_the_site_logo_and_the_colours_link(): void
    {
        SiteSetting::create(['site_name' => 'Mopane Heights', 'logo' => 'upload/site/logo.png']);

        $this->actingAs($this->superAdmin())->get('/admin/dashboard')
            ->assertSee('hms-brand-logo', false)->assertSee('upload/site/logo.png', false)->assertSee('Brand colours');
    }

    // ---------- Gallery update ----------

    public function test_updating_a_gallery_photo_keeps_only_the_newest_three_on_the_homepage(): void
    {
        $admin = $this->superAdmin();
        $photos = collect(range(1, 4))->map(fn ($i) => Gallery::create(['photo_name' => "upload/gallery/p{$i}.jpg", 'caption' => "Photo {$i}", 'show_on_home' => $i < 4]));

        $this->actingAs($admin)->post('/update/gallery', ['id' => $photos[3]->id, 'caption' => 'New view', 'show_on_home' => 1])
            ->assertRedirect('/all/gallery')->assertSessionHas('alert-type', 'success');

        $this->assertSame(3, Gallery::where('show_on_home', true)->count());
        $this->assertTrue((bool) $photos[3]->fresh()->show_on_home);
        $this->assertFalse((bool) $photos[0]->fresh()->show_on_home);
        $this->assertSame('New view', $photos[3]->fresh()->caption);
    }

    // ---------- Tables and forms ----------

    public function test_admin_lists_use_the_shared_table_class(): void
    {
        $admin = $this->superAdmin();
        $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(1), $this->date(2));

        foreach (['/booking/list', '/all/service', '/all/faq', '/all/team', '/all/testimonial', '/all/gallery', '/all/roles', '/all/permission', '/all/admin', '/view/room/list', '/all/blog/post', '/room/type/list'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk()->assertSee('hms-table', false);
        }
    }

    public function test_every_label_points_at_a_real_field_on_the_add_and_edit_pages(): void
    {
        $admin = $this->superAdmin();

        foreach (['/add/team', '/add/testimonial', '/add/admin', '/add/permission', '/add/roles', '/add/faq', '/add/gallery', '/add/service', '/site/setting', '/homepage/setting', '/smtp/setting', '/theme/setting', '/admin/profile', '/admin/change/password'] as $url) {
            $html = $this->actingAs($admin)->get($url)->assertOk()->getContent();
            preg_match_all('/<label[^>]*\sfor="([^"]+)"/', $html, $fors);
            preg_match_all('/\sid="([^"]+)"/', $html, $ids);
            foreach (array_unique($fors[1]) as $for) {
                $this->assertContains($for, $ids[1], "{$url}: label for=\"{$for}\" has no matching field");
            }
        }
    }

    public function test_form_buttons_use_action_words(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)->get('/add/team')->assertSee('Add team member')->assertDontSee('>Submit<', false);
        $this->actingAs($admin)->get('/add/roles')->assertSee('Add role');
    }

    private function palette(array $over = []): array
    {
        return array_merge(SiteSetting::THEME_DEFAULTS, $over);
    }
}
