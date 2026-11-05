<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    public function test_all_rooms_are_free_when_nothing_is_booked(): void
    {
        $room = $this->makeRoom(3);
        $this->assertSame(3, Booking::availableRooms($room->id, $this->date(10), $this->date(12)));
    }

    public function test_overlapping_bookings_reduce_availability_even_before_rooms_are_assigned(): void
    {
        $room = $this->makeRoom(3);
        $this->makeBooking($room, User::factory()->create(), $this->date(10), $this->date(13), ['number_of_rooms' => 2]);

        $this->assertSame(1, Booking::availableRooms($room->id, $this->date(11), $this->date(12)));
    }

    public function test_back_to_back_stays_do_not_clash(): void
    {
        $room = $this->makeRoom(1);
        $this->makeBooking($room, User::factory()->create(), $this->date(10), $this->date(12));

        // Check-out day of one guest is the check-in day of the next.
        $this->assertSame(1, Booking::availableRooms($room->id, $this->date(12), $this->date(14)));
        $this->assertSame(0, Booking::availableRooms($room->id, $this->date(11), $this->date(13)));
    }

    public function test_a_booking_can_ignore_itself_when_its_dates_are_edited(): void
    {
        $room = $this->makeRoom(1);
        $booking = $this->makeBooking($room, User::factory()->create(), $this->date(10), $this->date(12));

        $this->assertSame(1, Booking::availableRooms($room->id, $this->date(10), $this->date(13), $booking->id));
    }
}
