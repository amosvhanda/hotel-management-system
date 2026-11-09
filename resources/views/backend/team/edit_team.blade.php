@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Edit Team</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Edit Team</li>
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

            <form action="{{ route('team.update') }}" method="post" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="id" value="{{ $team->id }}">

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="f-name">Name</label>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="f-name" type="text" name="name" class="form-control" value="{{ $team->name }}"  />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="f-postion">Position</label>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="f-postion" type="text" name="postion"  class="form-control"  value="{{ $team->postion }}"  />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="f-facebook">Facebook</label>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="f-facebook" type="text" name="facebook" class="form-control"  value="{{ $team->facebook }}"   />
                    </div>
                </div>
               
               
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="image">Photo</label>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input class="form-control" name="image" type="file" id="image" accept="image/*">
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">  </h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <img id="showImage" src="{{ asset($team->image)  }}" alt="Preview" class="hms-preview">
                    </div>
                </div>
               
               
               
               
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9 text-secondary">
                        <input type="submit" class="btn btn-primary px-4" value="Save changes" />
                    </div>
                </div>
            </div>
        </form>

        </div>
            


    </div>
						</div>
					</div>
				</div>
			</div>

      <script type="text/javascript">

        $(document).ready(function(){
            $('#image').change(function(e){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#showImage').attr('src',e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });

        </script>      

@endsection