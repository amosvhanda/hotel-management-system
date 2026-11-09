@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content"> 
	<!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Bookings</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All Booking</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('add.room.list') }}" class="btn btn-primary px-5">Add Booking </a>
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->


    
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table align-middle hms-table" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Booking</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Stay</th>
                            <th class="text-end">Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($allData as $item)
                            <tr>
                                <td data-order="{{ $item->id }}">
                                    <a href="{{ route('edit_booking', $item->id) }}" class="fw-semibold">#{{ $item->code }}</a><br>
                                    <small class="text-secondary">{{ $item->created_at?->format('d M Y') }}</small>
                                </td>
                                <td>{{ $item->name }}<br><small class="text-secondary">{{ $item->phone }}</small></td>
                                <td>{{ $item->room->type->name ?? '—' }}<br><small class="text-secondary">{{ $item->number_of_rooms }} room(s) · {{ $item->persion }} guest(s)</small></td>
                                <td data-order="{{ $item->check_in }}">
                                    {{ \Carbon\Carbon::parse($item->check_in)->format('d M') }} – {{ \Carbon\Carbon::parse($item->check_out)->format('d M Y') }}<br>
                                    <small class="text-secondary">{{ $item->total_night }} night(s)</small>
                                </td>
                                <td class="text-end fw-semibold" data-order="{{ $item->total_price }}">${{ number_format((float) $item->total_price, 2) }}</td>
                                <td>
                                    @if ((int) $item->payment_status === 1)
                                        <span class="badge bg-success-subtle text-success">Paid</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Unpaid</span>
                                    @endif
                                    <br><small class="text-secondary">{{ $item->payment_method === 'COD' ? 'Cash on arrival' : $item->payment_method }}</small>
                                </td>
                                <td>
                                    @if ((int) $item->status === 1)
                                        <span class="badge bg-success-subtle text-success">Confirmed</span>
                                    @elseif ((int) $item->status === 2)
                                        <span class="badge bg-secondary-subtle text-secondary">Cancelled</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('edit_booking', $item->id) }}" class="btn btn-sm btn-primary">Manage</a>
                                    <a href="{{ route('download.invoice', $item->id) }}" class="btn btn-sm btn-light" title="Download invoice"><i class="bx bx-download"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
     
    <hr/>
     
</div>




@endsection