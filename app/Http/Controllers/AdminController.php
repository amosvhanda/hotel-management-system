<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function AdminDashboard()
    {
        $today = today();
        $monthStart = $today->copy()->startOfMonth();

        $totalRooms = \App\Models\RoomNumber::where('status', 'Active')->count();
        $occupiedToday = \App\Models\Booking::whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>', $today)
            ->sum('number_of_rooms');

        $stats = [
            'total_bookings'   => Booking::count(),
            'pending_bookings' => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'arrivals_today'   => Booking::whereDate('check_in', $today)->count(),
            'departures_today' => Booking::whereDate('check_out', $today)->count(),
            'revenue_month'    => Booking::where('payment_status', 1)->where('created_at', '>=', $monthStart)->sum('total_price'),
            'revenue_total'    => Booking::where('payment_status', 1)->sum('total_price'),
            'unpaid_amount'    => Booking::where('payment_status', 0)->whereDate('check_out', '>=', $today)->sum('total_price'),
            'bookings_today'   => Booking::whereDate('created_at', $today)->count(),
            'total_rooms'      => $totalRooms,
            'occupied_today'   => (int) $occupiedToday,
            'occupancy'        => $totalRooms ? min(100, round($occupiedToday / $totalRooms * 100)) : 0,
        ];

        // Last 12 months: bookings and paid revenue per month
        $months = collect(range(11, 0))->map(fn ($i) => $today->copy()->startOfMonth()->subMonths($i));
        $rows = Booking::where('created_at', '>=', $months->first())
            ->get(['created_at', 'total_price', 'payment_status'])
            ->groupBy(fn ($b) => $b->created_at->format('Y-m'));

        $chart = [
            'labels'   => $months->map->format('M Y')->values(),
            'bookings' => $months->map(fn ($m) => isset($rows[$m->format('Y-m')]) ? $rows[$m->format('Y-m')]->count() : 0)->values(),
            'revenue'  => $months->map(fn ($m) => isset($rows[$m->format('Y-m')]) ? round($rows[$m->format('Y-m')]->where('payment_status', 1)->sum('total_price'), 2) : 0)->values(),
        ];

        $arrivals = Booking::with('room.type')
            ->whereDate('check_in', '>=', $today)
            ->orderBy('check_in')
            ->limit(6)
            ->get();

        $recent = Booking::with('room.type')->latest('id')->limit(8)->get();

        return view('admin.index', compact('stats', 'chart', 'arrivals', 'recent'));
    }

    public function AdminLogout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    } // End Method

    public function AdminLogin(){

        return view('admin.admin_login');

    }// End Method

    public function AdminProfile(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_profile_view',compact('profileData'));

    }// End Method


    public function AdminProfileStore(Request $request){

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone'     => 'nullable|string|max:30',
            'address'   => 'nullable|string|max:255',
            'instagram' => 'nullable|url|max:255',
            'facebook'  => 'nullable|url|max:255',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
         $data->instagram = $request->instagram;
        $data->facebook = $request->facebook;

        if($request->file('photo')){
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_images/'.$data->photo));
            $filename = date('YmdHis') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/admin_images'),$filename);
            $data['photo'] = $filename;

        }
        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method


    public function AdminChangePassword(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_change_password',compact('profileData'));

    }// End Method

    public function AdminPasswordUpdate(Request $request){

        // Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        if(!Hash::check($request->old_password, auth::user()->password)){

            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );

            return back()->with($notification);

        }

        /// Update The New Password
        User::whereId(auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);

    }// End Method


    //////////// Admin User all Method//////////

    public function AllAdmin(){

        $alladmin = User::where('role','admin')->get();
        return view('backend.pages.admin.all_admin',compact('alladmin'));

    }// End Method

    public function AddAdmin(){

        $roles = Role::all();
        return view('backend.pages.admin.add_admin',compact('roles'));

    }// End Method

    public function StoreAdmin(Request $request){

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'password' => 'required|min:8',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password =  Hash::make($request->password);
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();

        if ($request->roles) {
           $user->assignRole($request->roles);
        }

        $notification = array(
            'message' => 'Admin User Created Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.admin')->with($notification);

    }// End Method


    public function EditAdmin($id){

        $user = User::find($id);
        $roles = Role::all();
        return view('backend.pages.admin.edit_admin',compact('user','roles'));

    }// End Method

    public function UpdateAdmin(Request $request,$id){

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();

        $user->roles()->detach();
        if ($request->roles) {
           $user->assignRole($request->roles);
        }

        $notification = array(
            'message' => 'Admin User Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.admin')->with($notification);

    }// End Method


    public function DeleteAdmin($id){

        if ((int) $id === (int) Auth::id()) {
            return back()->with(['message' => 'You cannot delete your own account.', 'alert-type' => 'error']);
        }

        $user = User::find($id);
        if (!is_null($user)) {
            $user->delete();
        }

        $notification = array(
            'message' => 'Admin User Delete Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method




}
