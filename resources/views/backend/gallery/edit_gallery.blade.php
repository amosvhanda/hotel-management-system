@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Edit Gallery </div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Edit Gallery</li>
							</ol>
						</nav>
					</div>
					 
				</div>
				<!--end breadcrumb-->
				<div class="container">
					<div class="main-body">
						<div class="row">
						 
    <div class="col-lg-12">
        
        <div class="card">

            <div class="card-body p-4">
                
       <form  class="row g-3" action="{{ route('update.gallery') }}" method="post" enctype="multipart/form-data">
         @csrf

         <input type="hidden" name="id" value="{{ $gallery->id }}">        
    
    <div class="col-md-6">
        <label for="f-photo_name" class="form-label">Gallery Image </label>
        <input id="f-photo_name" type="file" name="photo_name" class="form-control" accept="image/*">
        <img src="{{ asset($gallery->photo_name) }}" alt="" class="mt-3" style="width: 140px; height:90px; object-fit:cover;">
    </div>

    <div class="col-md-6">
        <label for="f-caption" class="form-label">Caption</label>
        <input id="f-caption" type="text" name="caption" class="form-control" maxlength="180" value="{{ old('caption', $gallery->caption) }}">
        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="show_on_home" value="1" id="show_on_home" {{ old('show_on_home', $gallery->show_on_home) ? 'checked' : '' }}>
            <label class="form-check-label" for="show_on_home">Show on homepage</label>
        </div>
        <small class="text-muted">The homepage uses up to three photos. A fourth replaces the oldest.</small>
    </div>
 
                 
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <button type="submit" class="btn btn-primary px-4">Save changes</button>
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>
						</div>
					</div>
				</div>
			</div>


           
 
         <!--------===Show MultiImage ========------->
<script>
    $(document).ready(function(){
     $('#multiImg').on('change', function(){ //on file input change
        if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
        {
            var data = $(this)[0].files; //this file data
             
            $.each(data, function(index, file){ //loop though each file
                if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                    var fRead = new FileReader(); //new filereader
                    fRead.onload = (function(file){ //trigger function on successful read
                    return function(e) {
                        var img = $('<img/>').addClass('thumb').attr('src', e.target.result) .width(100)
                    .height(80); //create image element 
                        $('#preview_img').append(img); //append image to output element
                    };
                    })(file);
                    fRead.readAsDataURL(file); //URL representing the file's data.
                }
            });
             
        }else{
            alert("Your browser doesn't support File API!"); //if File API is absent
        }
     });
    });
 </script>


@endsection