@extends('admin.admin_dashboard')
@section('title', 'Notifications')
@section('admin')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Notifications</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            @if ($unread)
                <form method="POST" action="{{ route('admin.notifications.read_all') }}">@csrf
                    <button class="btn btn-outline-primary"><i class="bx bx-check-double"></i> Mark all as read</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs border-0">
                <li class="nav-item"><a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('admin.notifications') }}">All</a></li>
                <li class="nav-item"><a class="nav-link {{ $filter === 'unread' ? 'active' : '' }}" href="{{ route('admin.notifications', ['filter' => 'unread']) }}">Unread ({{ $unread }})</a></li>
            </ul>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse ($notifications as $n)
                    <li class="list-group-item hms-row {{ $n->read_at ? '' : 'hms-unread' }}">
                        <div class="hms-note-icon {{ ($n->data['paid'] ?? false) ? 'is-paid' : '' }}"><i class="bx {{ $n->data['icon'] ?? (($n->data['paid'] ?? false) ? 'bx-check-circle' : 'bx-calendar-plus') }}"></i></div>
                        <div class="hms-row-main">
                            <a href="{{ route('admin.notifications.open', $n->id) }}" class="fw-semibold text-dark d-block">{{ $n->data['message'] ?? 'Notification' }}</a>
                            <small class="text-secondary">
                                @if (isset($n->data['check_in']))
                                    {{ \Carbon\Carbon::parse($n->data['check_in'])->format('d M') }} to {{ \Carbon\Carbon::parse($n->data['check_out'])->format('d M Y') }} ·
                                    ${{ number_format((float) ($n->data['total'] ?? 0), 2) }} ·
                                @endif
                                {{ $n->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @if (! $n->read_at)<span class="badge bg-warning-subtle text-warning">New</span>@endif
                    </li>
                @empty
                    <li class="list-group-item hms-empty">{{ $filter === 'unread' ? 'You are all caught up.' : 'No notifications yet. New and paid bookings will appear here.' }}</li>
                @endforelse
            </ul>
        </div>
        @if ($notifications->hasPages())
            <div class="card-footer">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>
@endsection
