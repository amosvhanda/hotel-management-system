@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">

    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Services</div>
<div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Services</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('add.service') }}" class="btn btn-primary px-4">Add Service</a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table align-middle hms-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="hms-col-num">#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $key => $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $service->display_image }}" alt="" class="hms-thumb">
                                    @unless ($service->image)<br><small class="text-secondary">auto image</small>@endunless
                                </td>
                                <td>{{ $service->title }}</td>
                                <td>{{ Str::limit($service->description, 60) }}</td>
                                <td>
                                    @can('service.edit')
                                        <a href="{{ route('edit.service', $service->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endcan

                                    @can('service.delete')
                                        <a href="{{ route('delete.service', $service->id) }}" class="btn btn-sm btn-outline-danger" id="delete">Delete</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
