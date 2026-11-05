@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit question</div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <form action="{{ route('faq.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $faq->id }}">
                        <div class="card-body">
                            @include('backend.faq._fields', ['faq' => $faq])
                            <button type="submit" class="btn btn-primary px-4">Save changes</button>
                            <a href="{{ route('all.faq') }}" class="btn btn-outline-dark ms-3 px-4">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
