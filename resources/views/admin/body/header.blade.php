<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>

              <div class="position-relative search-bar d-lg-block d-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
                <input class="form-control px-5" disabled type="search" placeholder="Search">
                <span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-5"><i class='bx bx-search'></i></span>
              </div>


              <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
                        <a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
                        </a>
                    </li>

                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                        </a>
                    </li>



                    @php
                        $me = Auth::user();
                        $ncount = $me->unreadNotifications()->count();
                        $latest = $me->notifications()->latest()->limit(8)->get();
                    @endphp
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" data-bs-toggle="dropdown" aria-label="Notifications, {{ $ncount }} unread">
                            @if ($ncount)<span class="alert-count" id="notification-count">{{ $ncount > 9 ? '9+' : $ncount }}</span>@endif
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end hms-notes">
                            <div class="msg-header">
                                <p class="msg-header-title">Notifications</p>
                                @if ($ncount)
                                    <form method="POST" action="{{ route('admin.notifications.read_all') }}" class="ms-auto">@csrf
                                        <button class="hms-note-all" type="submit">Mark all as read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="header-message-list d-none"></div>{{-- required by theme app.js --}}
                            <div class="header-notifications-list">
                                @forelse ($latest as $notification)
                                    <a class="dropdown-item {{ $notification->read_at ? 'is-read' : 'is-unread' }}" href="{{ route('admin.notifications.open', $notification->id) }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="hms-note-icon {{ ($notification->data['paid'] ?? false) ? 'is-paid' : '' }}"><i class="bx {{ ($notification->data['paid'] ?? false) ? 'bx-check-circle' : 'bx-calendar-plus' }}"></i></div>
                                            <div class="flex-grow-1 min-w-0">
                                                <p class="msg-name mb-0 text-wrap">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                                <small class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            @unless ($notification->read_at)<span class="hms-dot" aria-label="Unread"></span>@endunless
                                        </div>
                                    </a>
                                @empty
                                    <p class="hms-empty text-center mb-0">No notifications yet. New bookings will show up here.</p>
                                @endforelse
                            </div>
                            <a href="{{ route('admin.notifications') }}" class="hms-note-footer">See all notifications</a>
                        </div>
                    </li>
                </ul>
            </div>

            @php
                $id = Auth::user()->id;
                $profileData = App\Models\User::find($id);
            @endphp

            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" class="user-img" alt="user avatar">
                    <div class="user-info">
                        <p class="user-name mb-0">{{ $profileData->name }}</p>
                        <p class="designattion mb-0">{{ $profileData->email }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
  <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile') }}"><i class="bx bx-user fs-5"></i><span>Profile</span></a>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.change.password') }}"><i class="bx bx-cog fs-5"></i><span>Change Password</span></a>
                    </li>

                    </li>
                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>
  <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.logout') }}"><i class="bx bx-log-out-circle"></i><span>Logout</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
