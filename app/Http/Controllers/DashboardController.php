<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Admins have their own dashboard.
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $bookings = Booking::with('room.type', 'review')->where('user_id', Auth::id());

        $totalBookings = (clone $bookings)->count();
        $pendingBookings = (clone $bookings)->where('status', Booking::STATUS_PENDING)->count();
        $completedBookings = (clone $bookings)->where('status', Booking::STATUS_COMPLETE)->count();

        $nextStay = (clone $bookings)->whereDate('check_out', '>=', today())->orderBy('check_in')->first();
        $recentBookings = (clone $bookings)->latest('id')->limit(5)->get();

        return view('frontend.dashboard.user_dashboard', compact(
            'totalBookings', 'pendingBookings', 'completedBookings', 'nextStay', 'recentBookings'
        ));
    }
}
