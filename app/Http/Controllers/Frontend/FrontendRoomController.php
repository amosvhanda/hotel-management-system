<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookArea;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Room;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\RoomBookedDate;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;

class FrontendRoomController extends Controller
{
    /**
     * Filters shared by /rooms and the date search. Everything comes from the query string, so a
     * filtered page can be bookmarked, shared and reloaded.
     *
     * Prices and guest counts are stored as text, so every comparison and sort casts them to numbers
     * (otherwise "90" sorts after "250").
     *
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array, 2: array, 3: array}
     */
    protected function roomFilters(Request $request, int $minGuests = 0): array
    {
        $price = 'CAST(price AS DECIMAL(10,2))';
        $adults = 'CAST(total_adult AS SIGNED)';

        $active = Room::where('status', 1);

        $bounds = [
            'min' => (int) floor((float) (clone $active)->selectRaw("MIN($price) as v")->value('v')),
            'max' => (int) ceil((float) (clone $active)->selectRaw("MAX($price) as v")->value('v')),
        ];

        // Only offer guest counts that at least one room can take.
        $maxGuests = max(1, (int) (clone $active)->selectRaw("MAX($adults) as v")->value('v'));

        $distinct = fn (string $column) => (clone $active)->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->orderBy($column)->pluck($column);
        $beds = $distinct('bed_style');
        $views = $distinct('view');

        $facilities = Facility::whereIn('rooms_id', (clone $active)->pluck('id'))
            ->pluck('facility_name')
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique(fn ($name) => mb_strtolower($name))
            ->sortBy(fn ($name) => mb_strtolower($name))
            ->values();

        $wantedFacilities = collect((array) $request->input('facility', []))
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $facilities->contains(fn ($known) => mb_strtolower($known) === mb_strtolower($name)))
            ->values()
            ->all();

        $filters = [
            'persion'   => max($minGuests, min($maxGuests, (int) $request->input('persion', $minGuests))),
            'bed'       => $beds->contains($request->input('bed')) ? $request->input('bed') : '',
            'view'      => $views->contains($request->input('view')) ? $request->input('view') : '',
            'facility'  => $wantedFacilities,
            'max_price' => $request->filled('max_price')
                ? min($bounds['max'], max($bounds['min'], (int) round((float) $request->input('max_price'))))
                : $bounds['max'],
            'sort'      => in_array($request->input('sort'), ['asc', 'desc'], true) ? $request->input('sort') : '',
        ];

        $query = Room::with('type')->where('status', 1)
            ->when($filters['persion'] > 0, fn ($q) => $q->whereRaw("$adults >= ?", [$filters['persion']]))
            ->when($filters['bed'] !== '', fn ($q) => $q->where('bed_style', $filters['bed']))
            ->when($filters['view'] !== '', fn ($q) => $q->where('view', $filters['view']))
            ->when($filters['max_price'] < $bounds['max'], fn ($q) => $q->whereRaw("$price <= ?", [$filters['max_price']]))
            ->when($filters['sort'] !== '', fn ($q) => $q->orderByRaw("$price ".($filters['sort'] === 'desc' ? 'desc' : 'asc')), fn ($q) => $q->latest());

        foreach ($filters['facility'] as $name) {
            $query->whereHas('facilities', fn ($q) => $q->whereRaw('LOWER(TRIM(facility_name)) = ?', [mb_strtolower($name)]));
        }

        $options = ['beds' => $beds, 'views' => $views, 'facilities' => $facilities, 'maxGuests' => $maxGuests];

        return [$query, $filters, $bounds, $options];
    }

    /** Keep only room types that are free for the whole stay, and note how many are left. */
    protected function onlyAvailable($rooms, string $checkIn, string $checkOut)
    {
        $availability = Booking::availabilityMap($rooms->pluck('id')->all(), $checkIn, $checkOut);

        return $rooms
            ->filter(fn ($room) => ($availability[$room->id] ?? 0) > 0)
            ->each(fn ($room) => $room->setAttribute('rooms_left', $availability[$room->id]))
            ->values();
    }

    public function AllFrontendRoomList(Request $request){

        [$query, $filters, $bounds, $options] = $this->roomFilters($request);

        // Dates are optional here. When both are valid the list also shows what is actually free.
        $checkIn = $checkOut = '';
        $dateError = null;
        $nights = 0;
        $rooms = $query->get();

        if ($request->filled('check_in') || $request->filled('check_out')) {
            $validator = Validator::make($request->only('check_in', 'check_out'), [
                'check_in'  => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
            ], [
                'check_in.required'       => 'Choose a check-in date.',
                'check_out.required'      => 'Choose a check-out date.',
                'check_in.after_or_equal' => 'Check-in cannot be in the past.',
                'check_out.after'         => 'Check-out must be after check-in.',
            ]);

            if ($validator->fails()) {
                $dateError = $validator->errors()->first();
            } else {
                $checkIn = $request->input('check_in');
                $checkOut = $request->input('check_out');
                $nights = \App\Models\Booking::stayNights($checkIn, $checkOut);
                $rooms = $this->onlyAvailable($rooms, $checkIn, $checkOut);
            }
        }

        $guests = max(1, $filters['persion']);
        $stayQuery = $checkIn !== ''
            ? http_build_query(['check_in' => $checkIn, 'check_out' => $checkOut, 'persion' => $guests])
            : '';

        return view('frontend.room.all_rooms', compact('rooms', 'filters', 'bounds', 'options', 'checkIn', 'checkOut', 'dateError', 'nights', 'stayQuery'));
    } // End Method 


    public function RoomDetailsPage($id){

        $roomdetails = Room::findOrFail($id);
        $multiImage = MultiImage::where('rooms_id',$id)->get();
        $facility = Facility::where('rooms_id',$id)->get();

        $otherRooms = Room::where('id','!=', $id)->orderBy('id','DESC')->limit(2)->get();
        return view('frontend.room.room_details',compact('roomdetails','multiImage','facility','otherRooms'));

    } // End Method 


    public function BookingSeach(Request $request){

        $request->validate([
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'persion'   => 'nullable|integer|min:1',
        ], [
            'check_in.required'       => 'Please choose a check-in date.',
            'check_out.required'      => 'Please choose a check-out date.',
            'check_in.after_or_equal' => 'Check-in cannot be in the past.',
            'check_out.after'         => 'Check-out must be after check-in.',
        ]);

        $request->flash();

        [$query, $filters, $bounds, $options] = $this->roomFilters($request, 1);

        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $guests = $filters['persion'];

        $rooms = $this->onlyAvailable($query->get(), $checkIn, $checkOut);

        $nights = \App\Models\Booking::stayNights($checkIn, $checkOut);
        $stayQuery = http_build_query(['check_in' => $checkIn, 'check_out' => $checkOut, 'persion' => $guests]);

        return view('frontend.room.search_room', compact('rooms', 'filters', 'bounds', 'options', 'checkIn', 'checkOut', 'guests', 'nights', 'stayQuery'));

    } // End Method


    public function SearchRoomDetails(Request $request,$id){
        $request->flash();
        $roomdetails = Room::findOrFail($id);
        $multiImage = MultiImage::where('rooms_id',$id)->get();
        $facility = Facility::where('rooms_id',$id)->get();

        $otherRooms = Room::where('id','!=', $id)->orderBy('id','DESC')->limit(2)->get();
        $room_id = $id;
        return view('frontend.room.search_room_details',compact('roomdetails','multiImage','facility','otherRooms','room_id'));

    }// End Method 


    public function CheckRoomAvailability(Request $request){

        $request->validate([
            'room_id'   => 'required|exists:rooms,id',
            'check_in'  => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $available = Booking::availableRooms((int) $request->room_id, $request->check_in, $request->check_out);
        $nights = \App\Models\Booking::stayNights($request->check_in, $request->check_out);

        return response()->json(['available_room' => $available, 'total_nights' => $nights]);
    }// End Method


}
 