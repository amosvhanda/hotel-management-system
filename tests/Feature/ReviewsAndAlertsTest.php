<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class ReviewsAndAlertsTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    private function pastStay(?User $guest = null, array $attrs = []): Booking
    {
        $guest ??= User::factory()->create(['name' => 'Farai Mutasa']);

        return $this->makeBooking($this->makeRoom(2, ['type' => 'Executive Suite']), $guest, $this->date(-4), $this->date(-1), $attrs);
    }

    private function review(Booking $booking, array $data = [])
    {
        return $this->actingAs($booking->user)->post(route('user.review.store', $booking->id), array_merge([
            'rating' => 5, 'title' => 'Lovely stay', 'comment' => 'Quiet room, friendly staff and a great breakfast.',
        ], $data));
    }

    /* ---------- Guest: rate your stay ---------- */

    public function test_my_bookings_offers_rate_your_stay_after_check_out_only(): void
    {
        $guest = User::factory()->create();
        $past = $this->pastStay($guest);
        $future = $this->makeBooking($past->room, $guest, $this->date(10), $this->date(12));

        $html = $this->actingAs($guest)->get('/user/booking')->assertOk()->getContent();
        $this->assertStringContainsString(route('user.review.create', $past->id), $html);
        $this->assertStringNotContainsString(route('user.review.create', $future->id), $html);

        $this->get(route('user.review.create', $future->id))->assertRedirect(route('user.booking'));
        $this->get(route('user.review.create', $past->id))->assertOk()->assertSee('How was your stay?');
    }

    public function test_guest_review_is_saved_pending_and_admins_are_alerted(): void
    {
        $admin = $this->superAdmin();
        $booking = $this->pastStay();

        $this->review($booking)->assertRedirect(route('user.booking'))->assertSessionHas('alert-type', 'success');

        $review = Review::first();
        $this->assertSame(Review::PENDING, (int) $review->status);
        $this->assertSame($booking->rooms_id, $review->rooms_id);

        $alert = $admin->fresh()->notifications()->first();
        $this->assertSame('review_new', $alert->data['type']);
        $this->assertStringContainsString('5★ review from Farai Mutasa', $alert->data['message']);
    }

    public function test_one_review_per_stay_and_not_for_cancelled_or_other_peoples_bookings(): void
    {
        $booking = $this->pastStay();
        $this->review($booking);
        $this->review($booking)->assertSessionHas('alert-type', 'error');
        $this->assertSame(1, Review::count());

        $cancelled = $this->pastStay(null, ['status' => Booking::STATUS_CANCELLED]);
        $this->review($cancelled)->assertSessionHas('alert-type', 'error');

        $someoneElse = $this->pastStay();
        $this->actingAs(User::factory()->create())->post(route('user.review.store', $someoneElse->id), ['rating' => 5, 'comment' => 'Not my booking at all'])
            ->assertNotFound();
        $this->assertSame(1, Review::count());
    }

    public function test_review_needs_stars_and_a_real_comment(): void
    {
        $booking = $this->pastStay();
        $this->review($booking, ['rating' => 0, 'comment' => 'ok'])->assertSessionHasErrors(['rating', 'comment']);
        $this->assertSame(0, Review::count());
    }

    /* ---------- Staff: approve → shows on room page and homepage ---------- */

    public function test_pending_reviews_are_hidden_until_staff_publish_them(): void
    {
        $admin = $this->superAdmin();
        $booking = $this->pastStay();
        $this->review($booking, ['comment' => 'The pool and the breakfast were wonderful.']);
        $review = Review::first();

        auth()->logout();
        $this->get('/room/details/' . $booking->rooms_id)->assertOk()->assertDontSee('The pool and the breakfast were wonderful.');

        $this->actingAs($admin)->get(route('all.reviews', ['status' => 'pending']))->assertOk()->assertSee('The pool and the breakfast');
        $this->post(route('review.status', $review->id), ['status' => 1])->assertSessionHas('alert-type', 'success');
        $this->assertNotNull($review->fresh()->approved_at);

        auth()->logout();
        $this->get('/room/details/' . $booking->rooms_id)->assertOk()
            ->assertSee('The pool and the breakfast were wonderful.')
            ->assertSee('Farai M.')
            ->assertSee('5.0');
        $this->get('/')->assertOk()->assertSee('The pool and the breakfast were wonderful.');
    }

    public function test_staff_can_hide_and_delete_a_review(): void
    {
        $admin = $this->superAdmin();
        $booking = $this->pastStay();
        $this->review($booking, ['comment' => 'Hidden review text goes here.']);
        $review = Review::first();

        $this->actingAs($admin)->post(route('review.status', $review->id), ['status' => 1]);
        $this->post(route('review.status', $review->id), ['status' => 2]);
        auth()->logout();
        $this->get('/room/details/' . $booking->rooms_id)->assertDontSee('Hidden review text goes here.');

        $this->actingAs($admin)->post(route('review.delete', $review->id));
        $this->assertSame(0, Review::count());
        $this->assertTrue($booking->fresh()->guestCanReview(), 'After delete the guest can review again.');
    }

    public function test_guests_cannot_reach_the_review_admin(): void
    {
        $this->actingAs(User::factory()->create())->get(route('all.reviews'))->assertRedirect('dashboard');
        $this->post(route('review.status', 1), ['status' => 1])->assertRedirect('dashboard');
    }

    /* ---------- Alerts for comments and contact messages ---------- */

    public function test_blog_comment_and_contact_message_alert_staff_and_open_the_right_page(): void
    {
        $admin = $this->superAdmin();
        $cat = BlogCategory::create(['category_name' => 'News', 'category_slug' => 'news']);
        $post = BlogPost::create(['blogcat_id' => $cat->id, 'user_id' => $admin->id, 'post_titile' => 'Pool reopens', 'post_slug' => 'pool-reopens',
            'post_image' => 'x.jpg', 'short_descp' => 'Short', 'long_descp' => '<p>Long</p>']);

        $this->actingAs(User::factory()->create(['name' => 'Nyasha']))->post('/store/comment/', ['post_id' => $post->id, 'message' => 'Great news!']);
        auth()->logout();
        $this->post('/store/contact', ['name' => 'Tinashe', 'email' => 't@example.com', 'reason' => 'event', 'subject' => 'Wedding', 'message' => 'Hi']);

        $alerts = $admin->fresh()->notifications()->get()->keyBy(fn ($n) => $n->data['type']);
        $this->assertStringContainsString('Nyasha', $alerts['comment_new']->data['message']);
        $this->assertStringContainsString('Tinashe: Wedding', $alerts['contact_new']->data['message']);

        $this->actingAs($admin)->get(route('admin.notifications.open', $alerts['comment_new']->id))->assertRedirect(route('all.comment'));
        $this->get(route('admin.notifications.open', $alerts['contact_new']->id))->assertRedirect(route('contact.message'));
    }

    public function test_signed_out_reader_is_invited_to_log_in_and_returns_to_the_post(): void
    {
        $admin = User::factory()->admin()->create();
        $cat = BlogCategory::create(['category_name' => 'News', 'category_slug' => 'news']);
        BlogPost::create(['blogcat_id' => $cat->id, 'user_id' => $admin->id, 'post_titile' => 'Pool reopens', 'post_slug' => 'pool-reopens',
            'post_image' => 'x.jpg', 'short_descp' => 'Short', 'long_descp' => '<p>Long</p>']);
        $guest = User::factory()->create(['password' => bcrypt('secret-pass-123')]);

        $this->get('/blog/details/pool-reopens')->assertOk()->assertSee('to leave a comment', false)->assertDontSee('Plz');
        $this->post('/login', ['login' => $guest->email, 'password' => 'secret-pass-123'])
            ->assertRedirect(url('/blog/details/pool-reopens') . '#comments');
    }
}
