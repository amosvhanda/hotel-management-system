<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\MessageLog;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class FoundationsTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    public function test_app_runs_on_harare_time(): void
    {
        $this->assertSame('Africa/Harare', config('app.timezone'));
        $this->assertSame('Africa/Harare', now()->timezoneName);
    }

    public function test_booking_changes_are_audited_with_who_and_what(): void
    {
        $admin = $this->superAdmin();
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));

        $this->actingAs($admin)->post(route('update.booking.status', $booking->id), ['payment_status' => 1, 'status' => 1]);

        $log = AuditLog::where('subject_type', $booking->getMorphClass())->where('subject_id', $booking->id)->where('event', 'updated')->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame([0, 1], array_map('intval', $log->changes['status']));

        $this->get(route('edit_booking', $booking->id))->assertOk()->assertSee('Booking history')->assertSee('by ' . $admin->name);
    }

    public function test_audit_never_stores_passwords(): void
    {
        $user = User::factory()->create();
        $user->update(['password' => bcrypt('new-secret-123')]);
        $this->assertSame(0, AuditLog::whereJsonContains('changes', ['password'])->count());
        foreach (AuditLog::all() as $log) {
            $this->assertArrayNotHasKey('password', $log->changes ?? []);
        }
    }

    public function test_a_message_can_only_be_logged_once_per_booking(): void
    {
        $booking = $this->makeBooking($this->makeRoom(1), User::factory()->create(), $this->date(5), $this->date(7));
        MessageLog::create(['booking_id' => $booking->id, 'channel' => 'email', 'template' => 'pre_arrival', 'recipient' => 'a@b.c']);

        $this->expectException(QueryException::class);
        MessageLog::create(['booking_id' => $booking->id, 'channel' => 'email', 'template' => 'pre_arrival', 'recipient' => 'a@b.c']);
    }

    public function test_status_labels_and_colours_come_from_one_place(): void
    {
        $b = new Booking(['status' => Booking::STATUS_CANCELLED]);
        $this->assertSame('Cancelled', $b->statusLabel());
        $this->assertStringContainsString('secondary', $b->statusBadgeClass());
        $this->assertSame('Confirmed', (new Booking(['status' => 1]))->statusLabel());
    }

    public function test_guest_messages_page_lists_failed_jobs_and_can_retry(): void
    {
        $admin = $this->superAdmin();
        DB::table('failed_jobs')->insert([
            'uuid' => 'abc-123', 'connection' => 'database', 'queue' => 'default',
            'payload' => json_encode(['displayName' => 'App\\Mail\\PreArrival']), 'exception' => "Swift error: connection refused\n#0 trace",
            'failed_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('system.messages'))->assertOk()
            ->assertSee('PreArrival')->assertSee('connection refused');
        $this->post(route('system.forget', 'abc-123'))->assertSessionHas('alert-type', 'success');
        $this->assertSame(0, DB::table('failed_jobs')->count());
    }

    public function test_jobs_table_exists_for_the_queue_worker(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('jobs'));
    }
}
