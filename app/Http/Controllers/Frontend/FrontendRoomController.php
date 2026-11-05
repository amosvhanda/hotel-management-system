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

class FrontendRoomController extends Controller
{
    public function AllFrontendRoomList(){

        $rooms = Room::latest()->get();
        return view('frontend.room.all_rooms',compact('rooms'));
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

        $check_date_booking_ids = [];
        $rooms = Room::withCount('room_numbers')->with('type')->where('status', 1)->get();

        return view('frontend.room.search_room', compact('rooms', 'check_date_booking_ids'));

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
        $nights = Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out));

        return response()->json(['available_room' => $available, 'total_nights' => $nights]);
    }// End Method


}
 