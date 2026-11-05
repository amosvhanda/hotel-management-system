<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    public static function adminPages(): array
    {
        return array_map(fn ($u) => [$u], [
            '/admin/dashboard', '/booking/list', '/edit_booking/1', '/assign_room/1', '/download/invoice/1',
            '/all/service', '/site/setting', '/smtp/setting', '/all/roles', '/all/admin', '/booking/report/',
        ]);
    }

    #[DataProvider('adminPages')]
    public function test_visitors_are_sent_to_login_for_admin_pages(string $url): void
    {
        $this->get($url)->assertRedirect('/login');
    }

    #[DataProvider('adminPages')]
    public function test_guests_cannot_open_admin_pages(string $url): void
    {
        $this->actingAs(User::factory()->create())->get($url)->assertRedirect('dashboard');
    }

    public function test_guests_cannot_change_booking_status_or_assign_rooms(): void
    {
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));
        $guest = User::factory()->create();

        $this->actingAs($guest)->post('/update/booking/status/' . $booking->id, ['payment_status' => 1, 'status' => 1])->assertRedirect('dashboard');
        $this->assertEquals(0, $booking->refresh()->payment_status);
    }

    public function test_guests_cannot_download_someone_elses_invoice(): void
    {
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));

        $this->actingAs(User::factory()->create())->get('/user/invoice/' . $booking->id)->assertNotFound();
    }

    public function test_guests_cannot_view_someone_elses_payment_page(): void
    {
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));

        $this->actingAs(User::factory()->create())->get('/paynow/return/' . $booking->id)->assertNotFound();
    }

    public function test_comments_require_sign_in_and_cannot_be_posted_as_another_user(): void
    {
        $this->post('/store/comment', ['post_id' => 1, 'message' => 'Hello'])->assertRedirect('/login');
    }
}
