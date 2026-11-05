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
     * Whole nights between two dates. Always a positive integer, on Carbon 2 and Carbon 3 alike
     * (Carbon 3 made diffInDays() return a signed float).
     */
    public static function stayNights($checkIn, $checkOut): int
    {
        $start = Carbon::parse($checkIn)->startOfDay();
        $end = Carbon::parse($checkOut)->startOfDay();

        return (int) round(abs($start->diffInDays($end)));
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
     * Availability for many room types at once (three queries in total, however many rooms).
     * Same rule as availableRooms(): active room numbers minus rooms reserved by overlapping bookings.
     *
     * @param  array<int,int>  $roomIds
     * @return array<int,int>  room id => rooms still free for the whole stay
     */
    public static function availabilityMap(array $roomIds, string $checkIn, string $checkOut): array
    {
        if ($roomIds === []) {
            return [];
        }

        $nights = static::nightsBetween($checkIn, $checkOut);

        $totals = Room::withCount(['room_numbers' => fn ($q) => $q->where('status', 'Active')])
            ->whereIn('id', $roomIds)
            ->get()
            ->pluck('room_numbers_count', 'id');

        $pairs = RoomBookedDate::whereIn('room_id', $roomIds)
            ->whereIn('book_date', $nights)
            ->select('room_id', 'booking_id')
            ->distinct()
            ->get();

        $roomsPerBooking = static::whereIn('id', $pairs->pluck('booking_id')->unique())
            ->pluck('number_of_rooms', 'id');

        $reserved = [];
        foreach ($pairs as $pair) {
            $reserved[$pair->room_id] = ($reserved[$pair->room_id] ?? 0) + (int) ($roomsPerBooking[$pair->booking_id] ?? 0);
        }

        $map = [];
        foreach ($roomIds as $id) {
            $map[$id] = max(0, (int) ($totals[$id] ?? 0) - (int) ($reserved[$id] ?? 0));
        }

        return $map;
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
