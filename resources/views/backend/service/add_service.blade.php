@extends('admin.admin_dashboard')
@section('admin')


<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Add Service</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Service</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <form id="serviceForm" action="{{ route('service.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                                @endif

                                <!-- Service Name -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Service Name</h6>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" />
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Description</h6>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                @include('backend.service._icon_field')

                                <!-- Service Image -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Service Image</h6>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <input class="form-control" name="image" type="file" id="image">
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0"></h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <img id="showImage" src="{{ url('upload/no_image.jpg') }}" alt="Service Image" class="rounded-circle p-1 bg-primary" width="80">
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <button type="submit" class="btn btn-primary px-4">Save Service</button>
                                        <a href="{{ route('all.service') }}" class="btn btn-outline-dark ms-3 px-4">← Back</a>
                                    </div>
                                </div>

                            </div> <!-- end card-body -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation Script -->
<script type="text/javascript">
    $(document).ready(function () {
        $('#serviceForm').validate({
            rules: {
                name: {
                    required: true,
                },
                image: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: 'Please enter the service name',
                },
                image: {
                    required: 'Please select an image',
                },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>

<!-- Image Preview Script -->
<script type="text/javascript">
    $(document).ready(function () {
        $('#image').change(function (e) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>

@endsection
