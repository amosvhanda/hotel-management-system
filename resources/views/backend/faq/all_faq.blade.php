@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">FAQ</div>
<div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('add.faq') }}" class="btn btn-primary px-5">Add question</a>
        </div>
    </div>

    <hr>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle hms-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqs as $item)
                            <tr>
                                <td>{{ $item->sort_order }}</td>
                                <td>{{ $item->question }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($item->answer, 120) }}</td>
                                <td>
                                    <a href="{{ route('edit.faq', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="{{ route('delete.faq', $item->id) }}" class="btn btn-sm btn-outline-danger" id="delete">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
