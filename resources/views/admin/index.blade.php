@extends('admin.admin_dashboard')
@section('title', 'Dashboard')
@section('admin')

@php
    $money = fn ($v) => '$' . number_format((float) $v, 2);
    $money0 = fn ($v) => '$' . number_format((float) $v, 0);
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'morning' : ($hour < 17 ? 'afternoon' : 'evening');
    $change = $stats['revenue_change'];
    $paidBadge = fn ($b) => (int) $b->payment_status === 1
        ? '<span class="badge bg-success-subtle text-success">Paid</span>'
        : '<span class="badge bg-danger-subtle text-danger">Unpaid</span>';
    $statusBadge = fn ($b) => match ((int) $b->status) {
        1 => '<span class="badge bg-success-subtle text-success">Confirmed</span>',
        2 => '<span class="badge bg-secondary-subtle text-secondary">Cancelled</span>',
        default => '<span class="badge bg-warning-subtle text-warning">Pending</span>',
    };
    $maxType = max(1, (float) $byType->max('revenue'));
    $methodTotal = max(1, (float) $byMethod->sum('revenue'));
@endphp

<div class="page-content hms-dash">

    {{-- Header --}}
    <div class="hms-dash-head">
        <div>
            <h4>Good {{ $greeting }}, {{ \Illuminate\Support\Str::before(Auth::user()->name, ' ') }}</h4>
            <p class="text-secondary mb-0">{{ now()->format('l, d F Y') }}. {{ $stats['arrivals_today'] }} arriving and {{ $stats['departures_today'] }} leaving today.</p>
        </div>
        <div class="hms-dash-actions">
            <a href="{{ route('add.room.list') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New booking</a>
            <a href="{{ route('booking.list') }}" class="btn btn-outline-primary">All bookings</a>
            <a href="{{ route('booking.report') }}" class="btn btn-outline-primary">Reports</a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-3">
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-primary-subtle text-primary"><i class="bx bx-log-in-circle"></i></div>
                <div>
                    <p>Arrivals today</p>
                    <h3>{{ $stats['arrivals_today'] }}</h3>
                    <small>{{ $stats['departures_today'] }} departing today</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-info-subtle text-info"><i class="bx bx-bed"></i></div>
                <div>
                    <p>Occupancy tonight</p>
                    <h3>{{ $stats['occupancy'] }}%</h3>
                    <div class="progress mt-1" style="height:6px" role="progressbar" aria-valuenow="{{ $stats['occupancy'] }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $stats['occupancy'] }}%"></div></div>
                    <small>{{ $stats['occupied_today'] }} of {{ $stats['total_rooms'] }} rooms taken</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-success-subtle text-success"><i class="bx bx-dollar-circle"></i></div>
                <div>
                    <p>Revenue this month</p>
                    <h3>{{ $money0($stats['revenue_month']) }}</h3>
                    <small>
                        @if (! is_null($change))
                            <span class="hms-delta {{ $change >= 0 ? 'is-up' : 'is-down' }}">{{ $change >= 0 ? '+' : '' }}{{ $change }}%</span> vs last month
                        @else
                            {{ $money0($stats['revenue_total']) }} paid all-time
                        @endif
                    </small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-warning-subtle text-warning"><i class="bx bx-time-five"></i></div>
                <div>
                    <p>To collect</p>
                    <h3>{{ $money0($stats['unpaid_amount']) }}</h3>
                    <small>{{ $stats['pending_bookings'] }} booking(s) awaiting confirmation</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Revenue chart --}}
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h6 class="mb-0">Bookings and revenue, last 12 months</h6>
                    <span class="text-secondary small">Average room rate {{ $money($stats['adr']) }} · Revenue per available room {{ $money($stats['revpar']) }} (this month)</span>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:290px"><canvas id="bookingChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Needs attention --}}
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Needs your attention</h6>
                    <span class="badge {{ ($attention['pending']->count() + $attention['unpaid_soon']->count()) ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }}">{{ $attention['pending']->count() + $attention['unpaid_soon']->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach ($attention['pending'] as $a)
                            <li class="list-group-item hms-row">
                                <div class="hms-row-main">
                                    <a href="{{ route('edit_booking', $a->id) }}" class="fw-semibold text-dark d-block text-truncate">{{ $a->name }}</a>
                                    <small class="text-secondary">Confirm booking #{{ $a->code }} · {{ $a->room->type->name ?? 'Room' }}</small>
                                </div>
                                <a href="{{ route('edit_booking', $a->id) }}" class="btn btn-sm btn-light">Review</a>
                            </li>
                        @endforeach
                        @foreach ($attention['unpaid_soon'] as $a)
                            @continue($attention['pending']->contains('id', $a->id))
                            <li class="list-group-item hms-row">
                                <div class="hms-row-main">
                                    <a href="{{ route('edit_booking', $a->id) }}" class="fw-semibold text-dark d-block text-truncate">{{ $a->name }}</a>
                                    <small class="text-secondary">Unpaid, arrives {{ \Carbon\Carbon::parse($a->check_in)->format('d M') }} · {{ $money0($a->total_price) }}</small>
                                </div>
                                <a href="{{ route('edit_booking', $a->id) }}" class="btn btn-sm btn-light">Open</a>
                            </li>
                        @endforeach
                        @if (! $attention['pending']->count() && ! $attention['unpaid_soon']->count())
                            <li class="list-group-item hms-empty">Nothing waiting on you. New bookings that need confirming will show up here.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Next 14 nights --}}
    <div class="card mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0">Occupancy, next 14 nights</h6>
            <span class="text-secondary small">Share of your {{ $stats['total_rooms'] }} rooms already booked</span>
        </div>
        <div class="card-body">
            <div class="hms-forecast" role="list">
                @foreach ($forecast as $d)
                    <div class="hms-forecast-day {{ $d['pct'] >= 85 ? 'is-high' : '' }}" role="listitem" title="{{ $d['date']->format('l d M') }}: {{ $d['taken'] }} of {{ $stats['total_rooms'] }} rooms">
                        <span class="hms-forecast-pct">{{ $d['pct'] }}%</span>
                        <span class="hms-forecast-bar"><i style="height: {{ max(3, $d['pct']) }}%"></i></span>
                        <span class="hms-forecast-label">{{ $d['date']->format('D') }}<br>{{ $d['date']->format('j') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Arrivals / departures --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs border-0" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-arrivals" type="button" role="tab">Upcoming arrivals</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-departures" type="button" role="tab">Leaving today ({{ $departures->count() }})</button></li>
                    </ul>
                    <a href="{{ route('booking.list') }}" class="small">View all</a>
                </div>
                <div class="card-body p-0 tab-content">
                    <ul class="list-group list-group-flush tab-pane fade show active" id="tab-arrivals" role="tabpanel">
                        @forelse ($arrivals as $a)
                            <li class="list-group-item hms-row">
                                <div class="hms-date-chip"><strong>{{ \Carbon\Carbon::parse($a->check_in)->format('d') }}</strong><span>{{ \Carbon\Carbon::parse($a->check_in)->format('M') }}</span></div>
                                <div class="hms-row-main">
                                    <a href="{{ route('edit_booking', $a->id) }}" class="fw-semibold text-dark d-block text-truncate">{{ $a->name }}</a>
                                    <small class="text-secondary">{{ $a->room->type->name ?? 'Room' }} · {{ (int) $a->total_night }} night(s) · {{ $a->number_of_rooms }} room(s)</small>
                                </div>
                                {!! $paidBadge($a) !!}
                            </li>
                        @empty
                            <li class="list-group-item hms-empty">No arrivals booked yet. Bookings made on the website will appear here.</li>
                        @endforelse
                    </ul>
                    <ul class="list-group list-group-flush tab-pane fade" id="tab-departures" role="tabpanel">
                        @forelse ($departures as $a)
                            <li class="list-group-item hms-row">
                                <div class="hms-date-chip"><strong>{{ \Carbon\Carbon::parse($a->check_out)->format('d') }}</strong><span>{{ \Carbon\Carbon::parse($a->check_out)->format('M') }}</span></div>
                                <div class="hms-row-main">
                                    <a href="{{ route('edit_booking', $a->id) }}" class="fw-semibold text-dark d-block text-truncate">{{ $a->name }}</a>
                                    <small class="text-secondary">{{ $a->room->type->name ?? 'Room' }} · {{ $a->phone }}</small>
                                </div>
                                {!! $paidBadge($a) !!}
                            </li>
                        @empty
                            <li class="list-group-item hms-empty">Nobody is checking out today.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Room types --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Top room types</h6><small class="text-secondary">Paid revenue, last 90 days</small></div>
                <div class="card-body">
                    @forelse ($byType as $t)
                        <div class="hms-meter">
                            <div class="hms-meter-top"><span class="text-truncate">{{ $t['name'] }}</span><strong>{{ $money0($t['revenue']) }}</strong></div>
                            <div class="hms-meter-track"><i style="width: {{ round($t['revenue'] / $maxType * 100) }}%"></i></div>
                            <small class="text-secondary">{{ $t['bookings'] }} booking(s)</small>
                        </div>
                    @empty
                        <p class="hms-empty mb-0">No bookings in the last 90 days.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Payment methods --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">How guests pay</h6><small class="text-secondary">Paid bookings, last 90 days</small></div>
                <div class="card-body">
                    @forelse ($byMethod as $m)
                        <div class="hms-meter">
                            <div class="hms-meter-top"><span class="text-truncate">{{ $m['name'] }}</span><strong>{{ round($m['revenue'] / $methodTotal * 100) }}%</strong></div>
                            <div class="hms-meter-track"><i style="width: {{ round($m['revenue'] / $methodTotal * 100) }}%"></i></div>
                            <small class="text-secondary">{{ $m['count'] }} payment(s) · {{ $money0($m['revenue']) }}</small>
                        </div>
                    @empty
                        <p class="hms-empty mb-0">No paid bookings in the last 90 days.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Latest bookings --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Latest bookings</h6>
            <a href="{{ route('booking.list') }}" class="small">View all</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Stay</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $item)
                            <tr>
                                <td><a href="{{ route('edit_booking', $item->id) }}" class="fw-semibold">#{{ $item->code }}</a><br><small>{{ $item->created_at?->format('d M Y') }}</small></td>
                                <td>{{ $item->name }}<br><small>{{ $item->phone }}</small></td>
                                <td>{{ $item->room->type->name ?? '—' }}<br><small>{{ $item->number_of_rooms }} room(s)</small></td>
                                <td>{{ \Carbon\Carbon::parse($item->check_in)->format('d M') }} – {{ \Carbon\Carbon::parse($item->check_out)->format('d M') }}</td>
                                <td class="text-end fw-semibold">{{ $money($item->total_price) }}</td>
                                <td>{!! $statusBadge($item) !!} {!! $paidBadge($item) !!}</td>
                                <td class="text-end"><a href="{{ route('edit_booking', $item->id) }}" class="btn btn-sm btn-light">Manage</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="hms-empty text-center py-4">No bookings yet. Share your website link to get the first one.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var el = document.getElementById('bookingChart');
        if (!el || typeof Chart === 'undefined') return;
        var data = @json($chart);
        var css = getComputedStyle(document.documentElement);
        var c = function (n) { return css.getPropertyValue(n).trim(); };
        Chart.defaults.color = c('--hms-chart-text');
        Chart.defaults.borderColor = c('--hms-chart-grid');
        Chart.defaults.font.family = c('--hms-text').replace(/"/g, '');
        new Chart(el, {
            data: {
                labels: data.labels,
                datasets: [
                    { type: 'bar', label: 'Bookings', data: data.bookings, backgroundColor: c('--hms-chart-1'), borderRadius: 4, yAxisID: 'y' },
                    { type: 'line', label: 'Paid revenue ($)', data: data.revenue, borderColor: c('--hms-chart-2'), backgroundColor: c('--hms-chart-2'), borderWidth: 2.5, tension: .35, yAxisID: 'y1' }
                ]
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Bookings' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue ($)' } }
                }
            }
        });
    })();
</script>
@endpush
@endsection
