<div class="sidebar-wrapper" data-simplebar="true">
    @php($brand = App\Models\SiteSetting::current())
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="hms-brand" title="View website">
            <img class="hms-brand-mark" src="{{ $brand->favicon_url }}" alt="">
            <span class="hms-brand-full">
                @if ($brand->logo_light_url)
                    <img src="{{ $brand->logo_light_url }}" class="hms-brand-logo" alt="{{ $brand->name }}">
                @elseif ($brand->logo_url)
                    <span class="hms-brand-chip"><img src="{{ $brand->logo_url }}" class="hms-brand-logo" alt="{{ $brand->name }}"></span>
                @else
                    <span class="logo-text">{{ \Illuminate\Support\Str::limit($brand->name, 16) }}</span>
                @endif
            </span>
        </a>
        <button type="button" class="toggle-icon hms-collapse ms-auto" title="Collapse menu" aria-label="Collapse menu"><i class='bx bx-chevrons-left'></i></button>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

        <li>
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        @if(Auth::user()->can('team.menu'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-group"></i>
                </div>
                <div class="menu-title">Manage Teams </div>
            </a>
            <ul>
                @if(Auth::user()->can('team.all'))
                <li> <a href="{{ route('all.team') }}"><i class='bx bx-radio-circle'></i>All Team</a>
                </li>
                @endif
                @if(Auth::user()->can('team.add'))
                <li> <a href="{{ route('add.team') }}"><i class='bx bx-radio-circle'></i>Add Team</a>
                </li>
                @endif
            </ul>



        </li>
        @endif
@if(Auth::user()->can('service.menu'))
    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon">
                <i class="bx bx-concierge"></i>
            </div>
            <div class="menu-title">Manage Services</div>
        </a>
        <ul class="treeview-menu">
            @can('service.all')
            <li>
                <a href="{{ route('all.service') }}"><i class='bx bx-radio-circle'></i>All Services</a>
            </li>
            @endcan
            @can('service.add')
            <li>
                <a href="{{ route('add.service') }}"><i class='bx bx-radio-circle'></i>Add Service</a>
            </li>
            @endcan
        </ul>
    </li>
@endif

        @if(Auth::user()->can('bookarea.menu'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-calendar-check"></i>
                </div>
                <div class="menu-title">Manage Book Area </div>
            </a>
            <ul>
                @if(Auth::user()->can('update.bookarea'))
                <li> <a href="{{ route('book.area') }}"><i class='bx bx-radio-circle'></i>Update BookArea </a>
                </li>
                @endif

            </ul>
        </li>
        @endif

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-layer"></i>
                </div>
                <div class="menu-title">Manage Room Type </div>
            </a>
            <ul>
                <li> <a href="{{ route('room.type.list') }}"><i class='bx bx-radio-circle'></i>Room Type List </a>
                </li>

            </ul>
        </li>


        <li class="menu-label">Booking Manage </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-calendar"></i>
                </div>
                <div class="menu-title">Booking </div>
            </a>
            <ul>
                <li> <a href="{{ route('booking.list') }}"><i class='bx bx-radio-circle'></i>Booking List </a>
                </li>
                <li> <a href="{{ route('add.room.list') }}"><i class='bx bx-radio-circle'></i>Add Booking </a>
                </li>

            </ul>
        </li>
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-bed"></i>
                </div>
                <div class="menu-title">Manage RoomList</div>
            </a>
            <ul>
                <li> <a href="{{ route('view.room.list') }}"><i class='bx bx-radio-circle'></i>Room List</a>
                </li>


            </ul>
        </li>


        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-cog"></i>
                </div>
                <div class="menu-title">Setting</div>
            </a>
            <ul>
                <li> <a href="{{ route('smtp.setting') }}"><i class='bx bx-radio-circle'></i>SMTP Setting</a>
                </li>

                <li> <a href="{{ route('site.setting') }}"><i class='bx bx-radio-circle'></i>Site Setting</a>
                </li>
                <li> <a href="{{ route('theme.setting') }}"><i class='bx bx-radio-circle'></i>Brand colours</a>
                </li>
                <li> <a href="{{ route('homepage.setting') }}"><i class='bx bx-radio-circle'></i>Homepage</a>
                </li>


            </ul>
        </li>


        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-message-square-detail"></i>
                </div>
                <div class="menu-title">Testimonials</div>
            </a>
            <ul>
                <li> <a href="{{ route('all.testimonial') }}"><i class='bx bx-radio-circle'></i>All Testimonial</a>
                </li>

                <li> <a href="{{ route('add.testimonial') }}"><i class='bx bx-radio-circle'></i>Add Testimonial</a>
                </li>


            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-help-circle"></i>
                </div>
                <div class="menu-title">FAQ</div>
            </a>
            <ul>
                <li> <a href="{{ route('all.faq') }}"><i class='bx bx-radio-circle'></i>All Questions</a>
                </li>
                <li> <a href="{{ route('add.faq') }}"><i class='bx bx-radio-circle'></i>Add Question</a>
                </li>
            </ul>
        </li>


        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-news"></i>
                </div>
                <div class="menu-title">Blog</div>
            </a>
            <ul>
                <li> <a href="{{ route('blog.category') }}"><i class='bx bx-radio-circle'></i>Blog Category </a>
                </li>

                <li> <a href="{{ route('all.blog.post') }}"><i class='bx bx-radio-circle'></i>All Blog Post</a>
                </li>


            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-comment-detail"></i>
                </div>
                <div class="menu-title">Manage Comment</div>
            </a>
            <ul>
                <li> <a href="{{ route('all.comment') }}"><i class='bx bx-radio-circle'></i>All Comments </a>
                </li>

            </ul>
        </li>


        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-bar-chart-alt-2"></i>
                </div>
                <div class="menu-title">Booking Report </div>
            </a>
            <ul>
                <li> <a href="{{ route('booking.report') }}"><i class='bx bx-radio-circle'></i>Booking Report </a>
                </li>

            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-images"></i>
                </div>
                <div class="menu-title">Hotel Gallery </div>
            </a>
            <ul>
                <li> <a href="{{ route('all.gallery') }}"><i class='bx bx-radio-circle'></i>All Gallery </a>
                </li>

            </ul>
        </li>


        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-envelope"></i>
                </div>
                <div class="menu-title">Contact Message </div>
            </a>
            <ul>
                <li> <a href="{{ route('contact.message') }}"><i class='bx bx-radio-circle'></i>Contact Message </a>
                </li>

            </ul>
        </li>



        <li class="menu-label">Role & Permission </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-shield-quarter"></i>
                </div>
                <div class="menu-title">Role & Permission </div>
            </a>
            <ul>
                <li> <a href="{{ route('all.permission') }}"><i class='bx bx-radio-circle'></i>All Permission </a>
                </li>
                <li> <a href="{{ route('all.roles') }}"><i class='bx bx-radio-circle'></i>All Roles </a>
                </li>

                <li> <a href="{{ route('add.roles.permission') }}"><i class='bx bx-radio-circle'></i>Role In Permission </a>
                </li>

                <li> <a href="{{ route('all.roles.permission') }}"><i class='bx bx-radio-circle'></i>All Role In Permission </a>
                </li>

            </ul>
        </li>


        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-user-circle"></i>
                </div>
                <div class="menu-title">Manage Admin User </div>
            </a>
            <ul>
                <li> <a href="{{ route('all.admin') }}"><i class='bx bx-radio-circle'></i>All Admin </a>
                </li>
                <li> <a href="{{ route('add.admin') }}"><i class='bx bx-radio-circle'></i>Add Admin </a>
                </li>



            </ul>
        </li>


        <li class="menu-label">Others</li>


        <li>
            <a href="mailto:{{ App\Models\SiteSetting::current()->email }}">
                <div class="parent-icon"><i class="bx bx-support"></i>
                </div>
                <div class="menu-title">Support</div>
            </a>
        </li>
    </ul>
    <!--end navigation-->
</div>
