<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaynowGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaynowController extends Controller
{
    /**
     * Paynow posts payment status here (server to server). Always answer 200
     * so Paynow stops retrying; problems are logged instead.
     */
    public function result(Request $request, PaynowGateway $paynow)
    {
        try {
            $paynow->handleResult($request->all());
        } catch (\Throwable $e) {
            Log::error('Paynow result rejected', ['error' => $e->getMessage(), 'reference' => $request->input('reference')]);
        }

        return response('OK', 200);
    }

    /**
     * The guest lands here after paying (or while approving on their phone).
     */
    public function return(Request $request, PaynowGateway $paynow, $bookingId)
    {
        $booking = Booking::with('room.type')->where('user_id', Auth::id())->findOrFail($bookingId);

        if ((int) $booking->payment_status !== 1) {
            $paynow->refresh($booking);
            $booking->refresh();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'paid'   => (int) $booking->payment_status === 1,
                'status' => $booking->paynow_status,
            ]);
        }

        return view('frontend.checkout.paynow_return', compact('booking'));
    }
}
