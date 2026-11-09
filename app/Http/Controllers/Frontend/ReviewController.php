<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Support\StaffAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Guests rate their stay from My Bookings once check-out day has come. */
class ReviewController extends Controller
{
    public function create($bookingId)
    {
        $booking = Booking::with('room.type', 'review')->where('user_id', Auth::id())->findOrFail($bookingId);

        if ($booking->review) {
            return redirect()->route('user.booking')
                ->with(['message' => 'You have already reviewed this stay. Thank you!', 'alert-type' => 'info']);
        }
        if (! $booking->guestCanReview()) {
            return redirect()->route('user.booking')
                ->with(['message' => 'You can rate your stay from your check-out day.', 'alert-type' => 'error']);
        }

        return view('frontend.review.create', compact('booking'));
    }

    public function store(Request $request, $bookingId)
    {
        $booking = Booking::with('room.type')->where('user_id', Auth::id())->findOrFail($bookingId);

        if (! $booking->guestCanReview()) {
            return redirect()->route('user.booking')
                ->with(['message' => 'This stay can no longer be reviewed.', 'alert-type' => 'error']);
        }

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:120',
            'comment' => 'required|string|min:10|max:2000',
        ], [
            'rating.required'  => 'Choose a star rating.',
            'comment.required' => 'Tell us a little about your stay.',
            'comment.min'      => 'Please write at least 10 characters.',
        ]);

        $review = Review::create([
            'booking_id' => $booking->id,
            'user_id'    => Auth::id(),
            'rooms_id'   => $booking->rooms_id,
            'rating'     => (int) $data['rating'],
            'title'      => $data['title'] ?? null,
            'comment'    => $data['comment'],
            'status'     => Review::PENDING,
        ]);

        StaffAlerts::send(
            'review_new',
            "New {$review->rating}★ review from {$booking->name} — " . ($booking->room->type->name ?? 'Room'),
            route('all.reviews', ['status' => 'pending']),
            'bx-star'
        );

        return redirect()->route('user.booking')
            ->with(['message' => 'Thank you for your review! It will appear on the website once the hotel has checked it.', 'alert-type' => 'success']);
    }
}
