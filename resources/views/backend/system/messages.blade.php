@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">
    <div class="page-breadcrumb d-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Guest messages</div>
        <div class="ms-auto text-secondary small">{{ $waiting }} waiting to send</div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6>Failed — needs attention</h6>
            @if ($failedJobs->isEmpty())
                <p class="text-secondary mb-0">Nothing failed. 🎉</p>
            @else
            <div class="table-responsive"><table class="table align-middle hms-table">
                <thead><tr><th>What</th><th>Error</th><th>When</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @foreach ($failedJobs as $j)
                    <tr>
                        <td>{{ $j->name }}</td>
                        <td class="text-danger small text-wrap" style="max-width:420px">{{ $j->error }}</td>
                        <td class="text-nowrap small">{{ \Carbon\Carbon::parse($j->failed_at)->format('d M H:i') }}</td>
                        <td class="text-end text-nowrap">
                            <form method="POST" action="{{ route('system.retry', $j->uuid) }}" class="d-inline">@csrf<button class="btn btn-sm btn-primary">Send again</button></form>
                            <form method="POST" action="{{ route('system.forget', $j->uuid) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-secondary">Remove</button></form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h6>Recent messages to guests</h6>
            @if ($recent->isEmpty())
                <p class="text-secondary mb-0">No messages sent yet.</p>
            @else
            <div class="table-responsive"><table class="table align-middle hms-table">
                <thead><tr><th>Message</th><th>To</th><th>Booking</th><th>Status</th><th>When</th></tr></thead>
                <tbody>
                @foreach ($recent as $m)
                    <tr>
                        <td><i class="bx {{ $m->channel === 'whatsapp' ? 'bxl-whatsapp' : 'bx-envelope' }}"></i> {{ ucfirst(str_replace('_', ' ', $m->template)) }}</td>
                        <td class="small">{{ $m->recipient }}</td>
                        <td>@if ($m->booking)<a href="{{ route('edit_booking', $m->booking_id) }}">#{{ $m->booking->code }}</a>@endif</td>
                        <td><span class="badge {{ ['sent' => 'bg-success-subtle text-success', 'failed' => 'bg-danger-subtle text-danger', 'skipped' => 'bg-secondary-subtle text-secondary'][$m->status] ?? 'bg-warning-subtle text-warning' }}">{{ ucfirst($m->status) }}</span>
                            @if ($m->error)<div class="small text-danger text-wrap">{{ Str::limit($m->error, 120) }}</div>@endif</td>
                        <td class="small text-nowrap">{{ ($m->sent_at ?? $m->created_at)->format('d M H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            @endif
        </div>
    </div>
</div>
@endsection
