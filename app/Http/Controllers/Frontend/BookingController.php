<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\BookConfirm;
use App\Models\Booking;
use App\Models\BookingRoomList;
use App\Models\Room;
use App\Models\RoomNumber;
use App\Models\RoomBookedDate;
use App\Models\User;
use App\Notifications\BookingComplete;
use App\Services\PaynowGateway;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    private function alert(string $message, string $type = 'success'): array
    {
        return ['message' => $message, 'alert-type' => $type];
    }

    /* ------------------------------------------------------------------
     | Guest booking flow
     * ------------------------------------------------------------------ */

    public function Checkout()
    {
        $book_data = Session::get('book_date');
        $room = $book_data ? Room::with('type')->find($book_data['room_id']) : null;

        if (! $room) {
            return redirect()->route('froom.all')
                ->with($this->alert('Please choose your dates and a room first.', 'error'));
        }

        $nights = \App\Models\Booking::stayNights($book_data['check_in'], $book_data['check_out']);

        return view('frontend.checkout.checkout', compact('book_data', 'room', 'nights'));
    }

    public function BookingStore(Request $request)
    {
        $data = $request->validate([
            'room_id'         => 'required|exists:rooms,id',
            'check_in'        => 'required|date|after_or_equal:today',
            'check_out'       => 'required|date|after:check_in',
            'persion'         => 'required|integer|min:1',
            'number_of_rooms' => 'required|integer|min:1',
        ], [
            'check_in.after_or_equal' => 'Check-in cannot be in the past.',
            'check_out.after'         => 'Check-out must be after check-in.',
        ]);

        $checkIn = Carbon::parse($data['check_in'])->format('Y-m-d');
        $checkOut = Carbon::parse($data['check_out'])->format('Y-m-d');

        // Never trust the availability number sent by the browser.
        $available = Booking::availableRooms((int) $data['room_id'], $checkIn, $checkOut);

        $room = Room::findOrFail($data['room_id']);
        $perRoom = max(1, (int) ($room->room_capacity ?: $room->total_adult));
        if ($data['persion'] > $perRoom * $data['number_of_rooms']) {
            return back()->withInput()->with($this->alert(
                "One room sleeps up to {$perRoom} guest(s). Add another room for {$data['persion']} guests.", 'error'));
        }

        if ($available < $data['number_of_rooms']) {
            $msg = $available > 0
                ? "Only {$available} room(s) of this type are free for those dates."
                : 'Sorry, this room is fully booked for those dates.';

            return back()->withInput()->with($this->alert($msg, 'error'));
        }

        Session::put('book_date', [
            'room_id'         => (int) $data['room_id'],
            'check_in'        => $checkIn,
            'check_out'       => $checkOut,
            'persion'         => (int) $data['persion'],
            'number_of_rooms' => (int) $data['number_of_rooms'],
        ]);

        // Signed-out guests log in / register first, then land on checkout.
        if (! Auth::check()) {
            session()->put('url.intended', route('checkout'));

            return redirect()->route('login')
                ->with($this->alert('Your room is held in this session. Log in or create an account to finish booking.', 'info'));
        }

        return redirect()->route('checkout');
    }

    public function CheckoutStore(Request $request, PaynowGateway $paynow)
    {
        $book_data = Session::get('book_date');

        if (! $book_data) {
            return redirect()->route('froom.all')
                ->with($this->alert('Your booking session expired. Please search again.', 'error'));
        }

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'country'        => 'required|string|max:100',
            'phone'          => 'required|string|max:30',
            'address'        => 'required|string|max:255',
            'state'          => 'required|string|max:100',
            'zip_code'       => 'nullable|string|max:20',
            'payment_method' => 'required|in:paynow,ecocash,COD',
            // EcoCash = Econet numbers 077 / 078
            'paynow_phone'   => $request->payment_method === 'ecocash'
                ? ['required', 'regex:/^(\+?263|0)?7[78]\d{7}$/']
                : ['nullable'],
        ], [
            'payment_method.required' => 'Please choose Paynow, EcoCash or cash.',
            'payment_method.in'       => 'Please choose Paynow, EcoCash or cash.',
            'paynow_phone.required'   => 'Enter the EcoCash number to charge.',
            'paynow_phone.regex'      => 'Enter a valid EcoCash (Econet) number starting with 077 or 078, e.g. 0771234567.',
        ]);

        // Paynow (web page) and EcoCash (phone prompt) are both paid online through Paynow.
        $payOnline = in_array($request->payment_method, ['paynow', 'ecocash'], true);

        $room = Room::findOrFail($book_data['room_id']);

        if (Booking::availableRooms($room->id, $book_data['check_in'], $book_data['check_out']) < $book_data['number_of_rooms']) {
            Session::forget('book_date');

            return redirect()->route('froom.all')
                ->with($this->alert('Those rooms were just booked by someone else. Please pick other dates.', 'error'));
        }

        if ($payOnline && ! $paynow->configured()) {
            return back()->withInput()
                ->with($this->alert('Online payment is not available right now. Please choose cash and pay when you arrive.', 'error'));
        }

        $nights = \App\Models\Booking::stayNights($book_data['check_in'], $book_data['check_out']);
        $subtotal = $room->price * $nights * $book_data['number_of_rooms'];
        $discount = round(($room->discount / 100) * $subtotal, 2);
        $total = $subtotal - $discount;

        // Booking and its blocked dates are saved together, before any payment redirect.
        $booking = DB::transaction(function () use ($request, $book_data, $room, $nights, $subtotal, $discount, $total) {
            $booking = Booking::create([
                'rooms_id'        => $room->id,
                'user_id'         => Auth::id(),
                'check_in'        => $book_data['check_in'],
                'check_out'       => $book_data['check_out'],
                'persion'         => $book_data['persion'],
                'number_of_rooms' => $book_data['number_of_rooms'],
                'total_night'     => $nights,
                'actual_price'    => $room->price,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'total_price'     => $total,
                'payment_method'  => ['COD' => 'COD', 'ecocash' => 'EcoCash', 'paynow' => 'Paynow'][$request->payment_method],
                'transation_id'   => '',
                'payment_status'  => 0,
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'country'         => $request->country,
                'state'           => $request->state,
                'zip_code'        => $request->zip_code,
                'address'         => $request->address,
                'code'            => Booking::generateCode(),
                'status'          => Booking::STATUS_PENDING,
            ]);

            $booking->syncBookedDates();

            return $booking;
        });

        Session::forget('book_date');

        if ($payOnline) {
            // EcoCash sends a prompt to the phone; Paynow opens Paynow's own payment page.
            $phone = $request->payment_method === 'ecocash' ? $request->paynow_phone : null;
            $result = $paynow->start($booking, $phone, $request->payment_method);

            if (! $result['ok']) {
                // Release the dates so the failed attempt doesn't block the room.
                $booking->bookedDates()->delete();
                $booking->delete();

                return redirect()->route('froom.all')
                    ->with($this->alert('Paynow could not start the payment: ' . $result['error'], 'error'));
            }

            if ($result['redirect']) {
                return redirect()->away($result['redirect']);
            }

            // EcoCash: the customer approves on their phone.
            return redirect()->route('paynow.return', $booking->id)
                ->with($this->alert('Check your phone and enter your EcoCash PIN to approve the payment.', 'info'));
        }

        $this->notifyAdmins($booking);
        app(\App\Services\GuestMessenger::class)->newBooking($booking, 'booking_received');

        return redirect()->route('user.booking')
            ->with($this->alert("Booking #{$booking->code} received. Please pay in cash when you arrive at the hotel."));
    }

    public function UserBooking()
    {
        $allData = Booking::with('room.type', 'review')->where('user_id', Auth::id())->latest('id')->get();

        return view('frontend.dashboard.user_booking', compact('allData'));
    }

    public function UserCancel($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if (! $booking->guestCanCancel()) {
            return back()->with($this->alert('This booking can no longer be cancelled online. Please call the hotel.', 'error'));
        }

        $booking->cancelAndRelease();
        app(\App\Services\GuestMessenger::class)->send($booking, 'booking_cancelled');

        return back()->with($this->alert("Booking #{$booking->code} cancelled."));
    }

    public function UserInvoice($id)
    {
        // A guest may only download their own invoice.
        $editData = Booking::with('room')->where('user_id', Auth::id())->findOrFail($id);

        return $this->invoicePdf($editData);
    }

    /* ------------------------------------------------------------------
     | Admin booking management
     * ------------------------------------------------------------------ */

    public function BookingList()
    {
        $allData = Booking::with('room.type')->latest('id')->get();

        return view('backend.booking.booking_list', compact('allData'));
    }

    public function EditBooking($id)
    {
        $editData = Booking::with('room')->findOrFail($id);

        return view('backend.booking.edit_booking', compact('editData'));
    }

    public function UpdateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:0,1',
            'status'         => 'required|in:0,1,2',
        ]);

        $booking = Booking::findOrFail($id);
        $wasConfirmed = (int) $booking->status === Booking::STATUS_COMPLETE;
        $newStatus = (int) $request->status;

        if ($newStatus === Booking::STATUS_CANCELLED) {
            $booking->update(['payment_status' => $request->payment_status]);
            if (! $booking->isCancelled()) {
                $booking->cancelAndRelease();
                app(\App\Services\GuestMessenger::class)->send($booking, 'booking_cancelled');
            }

            return back()->with($this->alert('Booking cancelled. The room is free again for those dates.'));
        }

        // Re-opening a cancelled booking: block its dates again, if still free.
        if ($booking->isCancelled()) {
            $free = Booking::availableRooms($booking->rooms_id, $booking->check_in, $booking->check_out, $booking->id);
            if ($free < $booking->number_of_rooms) {
                return back()->with($this->alert("Can't re-open: only {$free} room(s) are free for those dates now.", 'error'));
            }
            $booking->syncBookedDates();
        }

        $booking->update([
            'payment_status' => $request->payment_status,
            'status'         => $newStatus,
        ]);

        $message = 'Booking updated successfully.';
        $type = 'success';

        // Email the guest when the booking is confirmed (once; sent in the background).
        if (! $wasConfirmed && (int) $booking->status === Booking::STATUS_COMPLETE) {
            $result = app(\App\Services\GuestMessenger::class)->send($booking->loadMissing('room.type'), 'booking_confirmed');
            $message .= match ($result) {
                'queued' => ' Confirmation email is on its way to the guest.',
                'disabled' => ' (Confirmation email is switched off in Guest messages.)',
                'already_sent' => '',
                default => ' But the confirmation email could not be queued — check Guest messages.',
            };
            if ($result === 'error' || $result === 'no_recipient') {
                $type = 'warning';
            }
        }

        return back()->with($this->alert($message, $type));
    }

    public function UpdateBooking(Request $request, $id)
    {
        $request->validate([
            'check_in'        => 'required|date',
            'check_out'       => 'required|date|after:check_in',
            'number_of_rooms' => 'required|integer|min:1',
        ]);

        $booking = Booking::with('room')->findOrFail($id);
        $checkIn = Carbon::parse($request->check_in)->format('Y-m-d');
        $checkOut = Carbon::parse($request->check_out)->format('Y-m-d');

        $available = Booking::availableRooms($booking->rooms_id, $checkIn, $checkOut, $booking->id);

        if ($available < $request->number_of_rooms) {
            return back()->with($this->alert("Only {$available} room(s) are free for those dates.", 'error'));
        }

        DB::transaction(function () use ($booking, $request, $checkIn, $checkOut) {
            $nights = \App\Models\Booking::stayNights($checkIn, $checkOut);
            $price = $booking->room->price ?? $booking->actual_price;
            $subtotal = $price * $nights * $request->number_of_rooms;
            $discount = round((($booking->room->discount ?? 0) / 100) * $subtotal, 2);

            $booking->update([
                'check_in'        => $checkIn,
                'check_out'       => $checkOut,
                'number_of_rooms' => $request->number_of_rooms,
                'total_night'     => $nights,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'total_price'     => $subtotal - $discount,
            ]);

            // Dates changed, so room assignments must be redone.
            BookingRoomList::where('booking_id', $booking->id)->delete();
            $booking->syncBookedDates();
        });

        return back()->with($this->alert('Booking dates updated. Please re-assign rooms.'));
    }

    public function AssignRoom($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);

        $nights = RoomBookedDate::where('booking_id', $booking_id)->pluck('book_date');

        $overlappingIds = RoomBookedDate::whereIn('book_date', $nights)
            ->where('room_id', $booking->rooms_id)
            ->distinct()
            ->pluck('booking_id');

        $takenRoomNumberIds = BookingRoomList::whereIn('booking_id', $overlappingIds)->pluck('room_number_id');

        $room_numbers = RoomNumber::where('rooms_id', $booking->rooms_id)
            ->whereNotIn('id', $takenRoomNumberIds)
            ->where('status', 'Active')
            ->get();

        return view('backend.booking.assign_room', compact('booking', 'room_numbers'));
    }

    public function AssignRoomStore($booking_id, $room_number_id)
    {
        $booking = Booking::findOrFail($booking_id);
        $roomNumber = RoomNumber::where('rooms_id', $booking->rooms_id)->findOrFail($room_number_id);

        if ($booking->isCancelled()) {
            return back()->with($this->alert('This booking is cancelled. Re-open it before assigning rooms.', 'error'));
        }

        if (BookingRoomList::where('booking_id', $booking_id)->count() >= $booking->number_of_rooms) {
            return back()->with($this->alert('All rooms for this booking are already assigned.', 'error'));
        }

        BookingRoomList::create([
            'booking_id'     => $booking->id,
            'room_id'        => $booking->rooms_id,
            'room_number_id' => $roomNumber->id,
        ]);

        return back()->with($this->alert('Room assigned successfully.'));
    }

    public function AssignRoomDelete($id)
    {
        BookingRoomList::findOrFail($id)->delete();

        return back()->with($this->alert('Room assignment removed.'));
    }

    public function DownloadInvoice($id)
    {
        return $this->invoicePdf(Booking::with('room')->findOrFail($id));
    }

    public function MarkAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $user->notifications()->where('id', $notificationId)->first()?->markAsRead();

        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }

    /* ------------------------------------------------------------------
     | Helpers
     * ------------------------------------------------------------------ */

    private function invoicePdf(Booking $editData)
    {
        return Pdf::loadView('backend.booking.booking_invoice', compact('editData'))
            ->setPaper('a4')
            ->setOption(['tempDir' => storage_path('app'), 'chroot' => public_path()])
            ->download('invoice-' . $editData->code . '.pdf');
    }

    public function notifyAdmins(Booking $booking): void
    {
        try {
            Notification::send(User::where('role', 'admin')->get(), new BookingComplete($booking));
        } catch (\Throwable $e) {
            Log::warning('Admin booking notification failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
        }
    }
}
