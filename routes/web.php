<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaynowController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\RoomController;
use App\Http\Controllers\Backend\TeamController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\CommentController;
use App\Http\Controllers\Backend\SystemController;
use App\Http\Controllers\Backend\GuestMessageController;
use App\Http\Controllers\Backend\ReviewController as AdminReviewController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\GalleryController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\RoomListController;
use App\Http\Controllers\Backend\RoomTypeController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Frontend\FrontendRoomController;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', [UserController::class, 'Index'])->name('home');
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class, 'robots'])->name('robots');
Route::view('/about', 'frontend.about.about')->name('about');
Route::get('/service/details/{id}', [ServiceController::class, 'ShowService'])->name('service.details');

Route::controller(FrontendRoomController::class)->group(function () {
    Route::get('/rooms/', 'AllFrontendRoomList')->name('froom.all');
    Route::get('/room/details/{id}', 'RoomDetailsPage')->name('room.details');
    Route::get('/bookings/', 'BookingSeach')->name('booking.search');
    Route::get('/search/room/details/{id}', 'SearchRoomDetails')->name('search_room_details');
    Route::get('/check_room_availability/', 'CheckRoomAvailability')->name('check_room_availability');
});

Route::controller(BlogController::class)->group(function () {
    Route::get('/blog', 'BlogList')->name('blog.list');
    Route::get('/blog/details/{slug}', 'BlogDetails')->name('blog.details');
    Route::get('/blog/cat/list/{id}', 'BlogCatList')->name('blog.cat.list');
});

Route::post('/store/comment/', [CommentController::class, 'StoreComment'])->name('store.comment')->middleware(['auth', 'throttle:10,1']);

Route::controller(GalleryController::class)->group(function () {
    Route::get('/gallery', 'ShowGallery')->name('show.gallery');
    Route::get('/contact', 'ContactUs')->name('contact.us');
    Route::post('/store/contact', 'StoreContactUs')->name('store.contact')->middleware('throttle:5,1');
});

// Paynow server-to-server status update. Must be public and CSRF-exempt (see VerifyCsrfToken).
// Saving the chosen stay works signed out too: checkout then asks the guest to
// log in or register and brings them straight back to checkout with it.
Route::post('/booking/store/', [BookingController::class, 'BookingStore'])->name('user_booking_store')->middleware('throttle:30,1');

Route::post('/paynow/result', [PaynowController::class, 'result'])->name('paynow.result');

require __DIR__.'/auth.php';

Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');

/*
|--------------------------------------------------------------------------
| Logged-in guests
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [UserController::class, 'UserProfile'])->name('user.profile');
    Route::post('/profile/store', [UserController::class, 'UserStore'])->name('profile.store');
    Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
    Route::get('/user/change/password', [UserController::class, 'UserChangePassword'])->name('user.change.password');
    Route::post('/password/change/password', [UserController::class, 'ChangePasswordStore'])->name('password.change.store');

    Route::controller(BookingController::class)->group(function () {
        Route::get('/checkout/', 'Checkout')->name('checkout');
        Route::post('/checkout/store/', 'CheckoutStore')->name('checkout.store')->middleware('throttle:10,1');

        Route::get('/user/booking', 'UserBooking')->name('user.booking');
        Route::get('/user/invoice/{id}', 'UserInvoice')->name('user.invoice');
        Route::post('/user/booking/{id}/cancel', 'UserCancel')->name('user.booking.cancel');

        Route::post('/mark-notification-as-read/{notification}', 'MarkAsRead')->name('notification.read');
    });

    // Guests rate their stay (from check-out day, one review per booking)
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/user/booking/{id}/review', 'create')->name('user.review.create');
        Route::post('/user/booking/{id}/review', 'store')->name('user.review.store')->middleware('throttle:10,1');
    });

    // Customer comes back here from Paynow
    Route::get('/paynow/return/{booking}', [PaynowController::class, 'return'])->name('paynow.return');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'roles:admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications');
    Route::get('/admin/notifications/{id}/open', [NotificationController::class, 'open'])->name('admin.notifications.open');
    Route::post('/admin/notifications/read-all', [NotificationController::class, 'readAll'])->name('admin.notifications.read_all');
    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');
    Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');

    // Team
    Route::controller(TeamController::class)->group(function () {
        Route::get('/all/team', 'AllTeam')->name('all.team')->middleware('permission:team.all');
        Route::get('/add/team', 'AddTeam')->name('add.team')->middleware('permission:team.add');
        Route::post('/team/store', 'StoreTeam')->name('team.store');
        Route::get('/edit/team/{id}', 'EditTeam')->name('edit.team');
        Route::post('/team/update', 'UpdateTeam')->name('team.update');
        Route::get('/delete/team/{id}', 'DeleteTeam')->name('delete.team');

        // Book area
        Route::get('/book/area', 'BookArea')->name('book.area');
        Route::post('/book/area/update', 'BookAreaUpdate')->name('book.area.update');
    });

    // Services
    Route::controller(ServiceController::class)->group(function () {
        Route::get('/all/service', 'AllService')->name('all.service')->middleware('permission:service.all');
        Route::get('/add/service', 'AddService')->name('add.service')->middleware('permission:service.add');
        Route::post('/service/store', 'StoreService')->name('service.store')->middleware('permission:service.add');
        Route::get('/edit/service/{id}', 'EditService')->name('edit.service')->middleware('permission:service.edit');
        Route::put('/service/update/{id}', 'UpdateService')->name('service.update')->middleware('permission:service.edit');
        Route::get('/delete/service/{id}', 'DeleteService')->name('delete.service')->middleware('permission:service.delete');
    });

    // Room types
    Route::controller(RoomTypeController::class)->group(function () {
        Route::get('/room/type/list', 'RoomTypeList')->name('room.type.list');
        Route::get('/add/room/type', 'AddRoomType')->name('add.room.type');
        Route::post('/room/type/store', 'RoomTypeStore')->name('room.type.store');
    });

    // Rooms
    Route::controller(RoomController::class)->group(function () {
        Route::get('/edit/room/{id}', 'EditRoom')->name('edit.room');
        Route::post('/update/room/{id}', 'UpdateRoom')->name('update.room');
        Route::get('/multi/image/delete/{id}', 'MultiImageDelete')->name('multi.image.delete');
        Route::post('/store/room/no/{id}', 'StoreRoomNumber')->name('store.room.no');
        Route::get('/edit/roomno/{id}', 'EditRoomNumber')->name('edit.roomno');
        Route::post('/update/roomno/{id}', 'UpdateRoomNumber')->name('update.roomno');
        Route::get('/delete/roomno/{id}', 'DeleteRoomNumber')->name('delete.roomno');
        Route::get('/delete/room/{id}', 'DeleteRoom')->name('delete.room');
    });

    // Bookings (admin side)
    Route::controller(BookingController::class)->group(function () {
        Route::get('/booking/list', 'BookingList')->name('booking.list');
        Route::get('/edit_booking/{id}', 'EditBooking')->name('edit_booking');
        Route::get('/download/invoice/{id}', 'DownloadInvoice')->name('download.invoice');
        Route::post('/update/booking/status/{id}', 'UpdateBookingStatus')->name('update.booking.status');
        Route::post('/update/booking/{id}', 'UpdateBooking')->name('update.booking');
        Route::get('/assign_room/{id}', 'AssignRoom')->name('assign_room');
        Route::get('/assign_room/store/{booking_id}/{room_number_id}', 'AssignRoomStore')->name('assign_room_store');
        Route::get('/assign_room_delete/{id}', 'AssignRoomDelete')->name('assign_room_delete');
    });

    // Room list
    Route::controller(RoomListController::class)->group(function () {
        Route::get('/view/room/list', 'ViewRoomList')->name('view.room.list');
        Route::get('/add/room/list', 'AddRoomList')->name('add.room.list');
        Route::post('/store/roomlist', 'StoreRoomList')->name('store.roomlist');
    });

    // Settings
    Route::controller(SettingController::class)->group(function () {
        Route::get('/smtp/setting', 'SmtpSetting')->name('smtp.setting');
        Route::post('/smtp/update', 'SmtpUpdate')->name('smtp.update');
        Route::get('/site/setting', 'SiteSetting')->name('site.setting');
        Route::post('/site/update', 'SiteUpdate')->name('site.update');
        Route::get('/theme/setting', 'Theme')->name('theme.setting');
        Route::post('/theme/update', 'ThemeUpdate')->name('theme.update');
        Route::post('/theme/reset', 'ThemeReset')->name('theme.reset');
        Route::get('/homepage/setting', 'Homepage')->name('homepage.setting');
        Route::post('/homepage/update', 'HomepageUpdate')->name('homepage.update');
    });

    Route::controller(FaqController::class)->group(function () {
        Route::get('/all/faq', 'AllFaq')->name('all.faq');
        Route::get('/add/faq', 'AddFaq')->name('add.faq');
        Route::post('/store/faq', 'StoreFaq')->name('faq.store');
        Route::get('/edit/faq/{id}', 'EditFaq')->name('edit.faq');
        Route::post('/update/faq', 'UpdateFaq')->name('faq.update');
        Route::get('/delete/faq/{id}', 'DeleteFaq')->name('delete.faq');
    });

    // Testimonials
    Route::controller(TestimonialController::class)->group(function () {
        Route::get('/all/testimonial', 'AllTestimonial')->name('all.testimonial');
        Route::get('/add/testimonial', 'AddTestimonial')->name('add.testimonial');
        Route::post('/store/testimonial', 'StoreTestimonial')->name('testimonial.store');
        Route::get('/edit/testimonial/{id}', 'EditTestimonial')->name('edit.testimonial');
        Route::post('/update/testimonial', 'UpdateTestimonial')->name('testimonial.update');
        Route::get('/delete/testimonial/{id}', 'DeleteTestimonial')->name('delete.testimonial');
    });

    // Blog
    Route::controller(BlogController::class)->group(function () {
        Route::get('/blog/category', 'BlogCategory')->name('blog.category');
        Route::post('/store/blog/category', 'StoreBlogCategory')->name('store.blog.category');
        Route::get('/edit/blog/category/{id}', 'EditBlogCategory');
        Route::post('/update/blog/category', 'UpdateBlogCategory')->name('update.blog.category');
        Route::get('/delete/blog/category/{id}', 'DeleteBlogCategory')->name('delete.blog.category');

        Route::get('/all/blog/post', 'AllBlogPost')->name('all.blog.post');
        Route::get('/add/blog/post', 'AddBlogPost')->name('add.blog.post');
        Route::post('/store/blog/post', 'StoreBlogPost')->name('store.blog.post');
        Route::get('/edit/blog/post/{id}', 'EditBlogPost')->name('edit.blog.post');
        Route::post('/update/blog/post', 'UpdateBlogPost')->name('update.blog.post');
        Route::get('/delete/blog/post/{id}', 'DeleteBlogPost')->name('delete.blog.post');
    });

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::get('/all/comment/', 'AllComment')->name('all.comment');
        Route::post('/update/comment/status', 'UpdateCommentStatus')->name('update.comment.status');
    });

    // Guest message settings (wording, on/off, WhatsApp number, test send)
    Route::get('/settings/guest-messages', [GuestMessageController::class, 'edit'])->name('guest.messages');
    Route::post('/settings/guest-messages', [GuestMessageController::class, 'update'])->name('guest.messages.update');
    Route::post('/settings/guest-messages/{key}/test', [GuestMessageController::class, 'test'])->name('guest.messages.test');

    // Guest messages: sent / failed / retry
    Route::controller(SystemController::class)->group(function () {
        Route::get('/system/messages', 'messages')->name('system.messages');
        Route::post('/system/messages/{uuid}/retry', 'retry')->name('system.retry');
        Route::post('/system/messages/{uuid}/forget', 'forget')->name('system.forget');
    });

    // Guest reviews
    Route::controller(AdminReviewController::class)->group(function () {
        Route::get('/all/reviews', 'index')->name('all.reviews');
        Route::post('/review/{id}/status', 'updateStatus')->name('review.status');
        Route::post('/review/{id}/delete', 'destroy')->name('review.delete');
    });

    // Reports
    Route::controller(ReportController::class)->group(function () {
        Route::get('/booking/report/', 'BookingReport')->name('booking.report');
        Route::post('/search-by-date', 'SearchByDate')->name('search-by-date');
    });

    // Gallery & contact messages
    Route::controller(GalleryController::class)->group(function () {
        Route::get('/all/gallery', 'AllGallery')->name('all.gallery');
        Route::get('/add/gallery', 'AddGallery')->name('add.gallery');
        Route::post('/store/gallery', 'StoreGallery')->name('store.gallery');
        Route::get('/edit/gallery/{id}', 'EditGallery')->name('edit.gallery');
        Route::post('/update/gallery', 'UpdateGallery')->name('update.gallery');
        Route::get('/delete/gallery/{id}', 'DeleteGallery')->name('delete.gallery');
        Route::post('/delete/gallery/multiple', 'DeleteGalleryMultiple')->name('delete.gallery.multiple');
        Route::get('/contact/message', 'AdminContactMessage')->name('contact.message');
    });

    // Roles & permissions
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/permission', 'AllPermission')->name('all.permission');
        Route::get('/add/permission', 'AddPermission')->name('add.permission');
        Route::post('/store/permission', 'StorePermission')->name('store.permission');
        Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
        Route::post('/update/permission', 'UpdatePermission')->name('update.permission');
        Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');
        Route::get('/import/permission', 'ImportPermission')->name('import.permission');
        Route::get('/export', 'Export')->name('export');
        Route::post('/import', 'Import')->name('import');

        Route::get('/all/roles', 'AllRoles')->name('all.roles');
        Route::get('/add/roles', 'AddRoles')->name('add.roles');
        Route::post('/store/roles', 'StoreRoles')->name('store.roles');
        Route::get('/edit/roles/{id}', 'EditRoles')->name('edit.roles');
        Route::post('/update/roles', 'UpdateRoles')->name('update.roles');
        Route::get('/delete/roles/{id}', 'DeleteRoles')->name('delete.roles');

        Route::get('/add/roles/permission', 'AddRolesPermission')->name('add.roles.permission');
        Route::post('/role/permission/store', 'RolePermissionStore')->name('role.permission.store');
        Route::get('/all/roles/permission', 'AllRolesPermission')->name('all.roles.permission');
        Route::get('/admin/edit/roles/{id}', 'AdminEditRoles')->name('admin.edit.roles');
        Route::post('/admin/roles/update/{id}', 'AdminRolesUpdate')->name('admin.roles.update');
        Route::get('/admin/delete/roles/{id}', 'AdminDeleteRoles')->name('admin.delete.roles');
    });

    // Admin users
    Route::controller(AdminController::class)->group(function () {
        Route::get('/all/admin', 'AllAdmin')->name('all.admin');
        Route::get('/add/admin', 'AddAdmin')->name('add.admin');
        Route::post('/store/admin', 'StoreAdmin')->name('store.admin');
        Route::get('/edit/admin/{id}', 'EditAdmin')->name('edit.admin');
        Route::post('/update/admin/{id}', 'UpdateAdmin')->name('update.admin');
        Route::get('/delete/admin/{id}', 'DeleteAdmin')->name('delete.admin');
    });
});
