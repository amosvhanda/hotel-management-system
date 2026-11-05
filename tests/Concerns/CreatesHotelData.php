<?php

namespace Tests\Concerns;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomNumber;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

/** Small, readable test data: a room type with N room numbers, users, bookings. */
trait CreatesHotelData
{
    protected function makeRoom(int $roomNumbers = 2, array $attrs = []): Room
    {
        $type = RoomType::create(['name' => $attrs['type'] ?? 'Deluxe Double Room']);
        unset($attrs['type']);

        $room = Room::create(array_merge([
            'roomtype_id'   => $type->id,
            'price'         => 100,
            'discount'      => 10,
            'room_capacity' => 2,
            'total_adult'   => 2,
            'total_child'   => 1,
            'size'          => 240,
            'view'          => 'Garden View',
            'bed_style'     => 'King Bed',
            'short_desc'    => 'A comfortable room.',
            'description'   => 'A comfortable room in Harare.',
            'status'        => 1,
        ], $attrs));

        for ($i = 1; $i <= $roomNumbers; $i++) {
            RoomNumber::create(['rooms_id' => $room->id, 'room_type_id' => $type->id, 'room_no' => 100 + $i, 'status' => 'Active']);
        }

        return $room;
    }

    protected function makeBooking(Room $room, User $user, string $checkIn, string $checkOut, array $attrs = []): Booking
    {
        $booking = Booking::create(array_merge([
            'rooms_id' => $room->id, 'user_id' => $user->id,
            'check_in' => $checkIn, 'check_out' => $checkOut,
            'persion' => 2, 'number_of_rooms' => 1, 'total_night' => 2,
            'actual_price' => 100, 'subtotal' => 200, 'discount' => 20, 'total_price' => 180,
            'payment_method' => 'COD', 'transation_id' => '', 'payment_status' => 0,
            'name' => 'Farai Mutasa', 'email' => 'farai@example.com', 'phone' => '0771234567',
            'country' => 'Zimbabwe', 'state' => 'Harare', 'address' => 'Borrowdale',
            'code' => Booking::generateCode(), 'status' => 0,
        ], $attrs));
        $booking->syncBookedDates();

        return $booking;
    }

    protected function superAdmin(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->admin()->create();
        $admin->assignRole('Super Admin');

        return $admin;
    }

    protected function date(int $daysFromToday): string
    {
        return now()->addDays($daysFromToday)->toDateString();
    }
}
