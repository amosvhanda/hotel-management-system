@extends('admin.admin_dashboard')
@section('admin')


<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Service</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Service</li>
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
                        {{-- Edit Service Form --}}
                        <form id="serviceForm" action="{{ route('service.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                                @endif

                                <!-- Service Name -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <label class="form-label mb-0" for="f-title">Service Name</label>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <input id="f-title" type="text" name="title" class="form-control" value="{{ old('title', $service->title) }}" />
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <label class="form-label mb-0" for="f-description">Description</label>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <textarea id="f-description" name="description" class="form-control" rows="4">{{ old('description', $service->description) }}</textarea>
                                    </div>
                                </div>

                                @include('backend.service._icon_field', ['service' => $service])

                                <!-- Service Image -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <label class="form-label mb-0" for="image">Service Image</label>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <input class="form-control" name="image" type="file" id="image" accept="image/*" />
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <!-- empty label for alignment -->
                                        <h6 class="mb-0"></h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <img id="showImage"
                                             src="{{ $service->display_image }}"
                                             alt="Service Image" class="rounded p-1 bg-light" width="160" />
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <button type="submit" class="btn btn-primary px-4">Update Service</button>
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
                title: {
                    required: true,
                },
                // image not required on edit
            },
            messages: {
                title: {
                    required: 'Please enter the service name',
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
            };
            reader.readAsDataURL(e.target.files[0]);
        });
    });
</script>

@endsection
