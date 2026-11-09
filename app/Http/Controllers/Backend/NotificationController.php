<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';
        $query = $filter === 'unread' ? Auth::user()->unreadNotifications() : Auth::user()->notifications();

        return view('backend.notification.index', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'filter' => $filter,
            'unread' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /** Mark one as read, then go to what it is about (booking, review, comment or message). */
    public function open(string $id)
    {
        $n = Auth::user()->notifications()->where('id', $id)->first();
        abort_if(! $n, 404);
        $n->markAsRead();

        if (! empty($n->data['url'])) {
            return redirect($n->data['url']); // review / comment / contact message
        }

        $bookingId = $n->data['booking_id'] ?? null;

        return $bookingId ? redirect()->route('edit_booking', $bookingId) : redirect()->route('admin.notifications');
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with(['message' => 'All notifications marked as read.', 'alert-type' => 'success']);
    }
}
