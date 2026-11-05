<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    // status: 0 = pending, 1 = complete/confirmed
    // payment_status: 0 = unpaid, 1 = paid
    public const STATUS_PENDING = 0;
    public const STATUS_COMPLETE = 1;

    public function assign_rooms()
    {
        return $this->hasMany(BookingRoomList::class, 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'rooms_id', 'id');
    }

    public function bookedDates()
    {
        return $this->hasMany(RoomBookedDate::class, 'booking_id');
    }

    /**
     * Every night between check-in (inclusive) and check-out (exclusive), as Y-m-d strings.
     */
    public static function nightsBetween(string $checkIn, string $checkOut): array
    {
        $start = Carbon::parse($checkIn)->startOfDay();
        $end = Carbon::parse($checkOut)->startOfDay()->subDay();

        if ($end->lt($start)) {
            return [];
        }

        return collect(CarbonPeriod::create($start, $end))
            ->map(fn ($day) => $day->format('Y-m-d'))
            ->all();
    }

    /**
     * How many rooms of this room type are still free for the whole stay.
     * Counts the rooms reserved by overlapping bookings, not just the rooms already assigned.
     */
    public static function availableRooms(int $roomId, string $checkIn, string $checkOut, ?int $ignoreBookingId = null): int
    {
        $room = Room::withCount(['room_numbers' => fn ($q) => $q->where('status', 'Active')])->find($roomId);

        if (! $room) {
            return 0;
        }

        $nights = static::nightsBetween($checkIn, $checkOut);

        $overlappingIds = RoomBookedDate::where('room_id', $roomId)
            ->whereIn('book_date', $nights)
            ->when($ignoreBookingId, fn ($q) => $q->where('booking_id', '!=', $ignoreBookingId))
            ->distinct()
            ->pluck('booking_id');

        $reserved = static::whereIn('id', $overlappingIds)->sum('number_of_rooms');

        return max(0, (int) $room->room_numbers_count - (int) $reserved);
    }

    /**
     * (Re)write the nightly rows that block this booking's dates.
     */
    public function syncBookedDates(): void
    {
        $this->bookedDates()->delete();

        foreach (static::nightsBetween($this->check_in, $this->check_out) as $night) {
            RoomBookedDate::create([
                'booking_id' => $this->id,
                'room_id' => $this->rooms_id,
                'book_date' => $night,
            ]);
        }
    }

    public static function generateCode(): string
    {
        do {
            $code = (string) random_int(100000000, 999999999);
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
