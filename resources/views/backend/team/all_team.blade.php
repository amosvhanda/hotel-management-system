@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">

    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Team</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('add.team') }}" class="btn btn-primary px-5">Add Team</a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Facebook</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($team as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ asset($item->image ?? 'upload/no_image.jpg') }}" alt="Team Image" style="width:70px; height:40px;">
                                </td>
                                <td>{{ $item->name ?? 'N/A' }}</td>
                                <td>{{ $item->postion ?? 'N/A' }}</td>
                                <td>
                                    @if($item->facebook)
                                        <a href="{{ $item->facebook }}" target="_blank">{{ $item->facebook }}</a>
                                    @else
                                        <span class="text-muted">No link</span>
                                    @endif
                                </td>
                                <td>
                                    @can('team.edit')
                                        <a href="{{ route('edit.team', $item->id) }}" class="btn btn-warning btn-sm radius-30">Edit</a>
                                    @endcan

                                    @can('team.delete')
                                        <a href="{{ route('delete.team', $item->id) }}" class="btn btn-danger btn-sm radius-30" id="delete">Delete</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No team members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr/>

</div>
@endsection
