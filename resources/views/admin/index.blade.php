@extends('admin.admin_dashboard')
@section('title', 'Dashboard')
@section('admin')

@php
    $money = fn ($v) => '$' . number_format((float) $v, 2);
    $badge = function ($b) {
        $s = (int) $b->status === 1 ? '<span class="badge bg-success-subtle text-success">Confirmed</span>' : '<span class="badge bg-warning-subtle text-warning">Pending</span>';
        $p = (int) $b->payment_status === 1 ? '<span class="badge bg-success-subtle text-success">Paid</span>' : '<span class="badge bg-danger-subtle text-danger">Unpaid</span>';
        return $s . ' ' . $p;
    };
@endphp

<div class="page-content">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="mb-0">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ \Illuminate\Support\Str::before(Auth::user()->name, ' ') }}</h4>
            <p class="text-secondary mb-0">{{ now()->format('l, d F Y') }} · here's what's happening at the hotel today.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('booking.list') }}" class="btn btn-outline-primary"><i class="bx bx-list-ul"></i> All bookings</a>
            <a href="{{ route('booking.report') }}" class="btn btn-primary"><i class="bx bx-bar-chart-alt-2"></i> Reports</a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-3">
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-primary-subtle text-primary"><i class="bx bx-log-in-circle"></i></div>
                <div>
                    <p>Arrivals today</p>
                    <h3>{{ $stats['arrivals_today'] }}</h3>
                    <small>{{ $stats['departures_today'] }} departure(s) today</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-info-subtle text-info"><i class="bx bx-bed"></i></div>
                <div>
                    <p>Occupancy tonight</p>
                    <h3>{{ $stats['occupancy'] }}%</h3>
                    <div class="progress mt-1" style="height:6px"><div class="progress-bar bg-info" style="width: {{ $stats['occupancy'] }}%"></div></div>
                    <small>{{ $stats['occupied_today'] }} of {{ $stats['total_rooms'] }} rooms</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-success-subtle text-success"><i class="bx bx-dollar-circle"></i></div>
                <div>
                    <p>Revenue this month</p>
                    <h3>{{ $money($stats['revenue_month']) }}</h3>
                    <small>{{ $money($stats['revenue_total']) }} paid all-time</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="hms-stat-card">
                <div class="hms-stat-icon bg-warning-subtle text-warning"><i class="bx bx-time-five"></i></div>
                <div>
                    <p>Pending bookings</p>
                    <h3>{{ $stats['pending_bookings'] }}</h3>
                    <small>{{ $money($stats['unpaid_amount']) }} still to collect</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card radius-10 h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Bookings &amp; revenue — last 12 months</h6>
                    <span class="text-secondary small">{{ $stats['total_bookings'] }} bookings in total · {{ $stats['bookings_today'] }} today</span>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:300px"><canvas id="bookingChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card radius-10 h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Upcoming arrivals</h6>
                    <a href="{{ route('booking.list') }}" class="small">View all</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($arrivals as $a)
                            <li class="list-group-item d-flex align-items-center gap-3">
                                <div class="hms-date-chip">
                                    <strong>{{ \Carbon\Carbon::parse($a->check_in)->format('d') }}</strong>
                                    <span>{{ \Carbon\Carbon::parse($a->check_in)->format('M') }}</span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <a href="{{ route('edit_booking', $a->id) }}" class="fw-semibold text-dark d-block text-truncate">{{ $a->name }}</a>
                                    <small class="text-secondary">{{ $a->room->type->name ?? 'Room' }} · {{ $a->total_night }} night(s)</small>
                                </div>
                                {!! (int) $a->payment_status === 1 ? '<span class="badge bg-success-subtle text-success">Paid</span>' : '<span class="badge bg-danger-subtle text-danger">Unpaid</span>' !!}
                            </li>
                        @empty
                            <li class="list-group-item text-center text-secondary py-5">No upcoming arrivals.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card radius-10 mt-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Latest bookings</h6>
            <a href="{{ route('booking.list') }}" class="small">View all</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
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
                                <td><a href="{{ route('edit_booking', $item->id) }}" class="fw-semibold">#{{ $item->code }}</a><br><small class="text-secondary">{{ $item->created_at?->format('d M Y') }}</small></td>
                                <td>{{ $item->name }}<br><small class="text-secondary">{{ $item->phone }}</small></td>
                                <td>{{ $item->room->type->name ?? '—' }}<br><small class="text-secondary">{{ $item->number_of_rooms }} room(s)</small></td>
                                <td>{{ \Carbon\Carbon::parse($item->check_in)->format('d M') }} – {{ \Carbon\Carbon::parse($item->check_out)->format('d M') }}</td>
                                <td class="text-end fw-semibold">{{ $money($item->total_price) }}</td>
                                <td>{!! $badge($item) !!}</td>
                                <td class="text-end"><a href="{{ route('edit_booking', $item->id) }}" class="btn btn-sm btn-light">Manage</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-secondary py-4">No bookings yet.</td></tr>
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
        new Chart(el, {
            data: {
                labels: data.labels,
                datasets: [
                    { type: 'bar', label: 'Bookings', data: data.bookings, backgroundColor: 'rgba(13,110,253,.75)', borderRadius: 6, yAxisID: 'y' },
                    { type: 'line', label: 'Paid revenue ($)', data: data.revenue, borderColor: '#198754', backgroundColor: '#198754', tension: .35, yAxisID: 'y1' }
                ]
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'bottom' } },
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
