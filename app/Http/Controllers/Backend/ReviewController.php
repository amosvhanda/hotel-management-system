<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

/** Staff approve, hide or delete guest reviews. */
class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $filter = in_array($request->query('status'), ['pending', 'approved', 'hidden'], true) ? $request->query('status') : 'all';
        $map = ['pending' => Review::PENDING, 'approved' => Review::APPROVED, 'hidden' => Review::HIDDEN];

        $reviews = Review::with('user', 'booking', 'room.type')
            ->when($filter !== 'all', fn ($q) => $q->where('status', $map[$filter]))
            ->latest('id')
            ->get();

        $counts = [
            'all'      => Review::count(),
            'pending'  => Review::where('status', Review::PENDING)->count(),
            'approved' => Review::where('status', Review::APPROVED)->count(),
            'hidden'   => Review::where('status', Review::HIDDEN)->count(),
        ];

        return view('backend.review.all_reviews', compact('reviews', 'filter', 'counts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:0,1,2']);
        $review = Review::findOrFail($id);
        $status = (int) $request->status;

        $review->update([
            'status'      => $status,
            'approved_at' => $status === Review::APPROVED ? ($review->approved_at ?? now()) : $review->approved_at,
        ]);

        $msg = [Review::APPROVED => 'Review published on the website.', Review::HIDDEN => 'Review hidden from the website.', Review::PENDING => 'Review moved back to pending.'][$status];

        return back()->with(['message' => $msg, 'alert-type' => 'success']);
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();

        return back()->with(['message' => 'Review deleted. The guest can review that stay again.', 'alert-type' => 'success']);
    }
}
