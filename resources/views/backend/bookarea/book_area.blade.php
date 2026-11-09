@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Update Book Area </div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Update Book Area</li>
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

            <form   action="{{ route('book.area.update') }}" method="post" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="id" value="{{ $book->id }}">

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="f-short_title">Short Title</label>
                    </div>
                    <div class="form-group col-sm-9 text-secondary">
                        <input id="f-short_title" type="text" name="short_title" class="form-control" value="{{ $book->short_title }}"  />
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="f-main_title">Main Title</label>
                    </div>
                    <div class="form-group col-sm-9 text-secondary">
                        <input id="f-main_title" type="text" name="main_title"  class="form-control" value="{{ $book->main_title }}" />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="input40">Short Description</label>
                    </div>
                    <div class="form-group col-sm-9 text-secondary">
        <textarea class="form-control" id="input40" name="short_desc" rows="3" placeholder="Description"> {{ $book->short_desc }} </textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="f-link_url">Link Url</label>
                    </div>
                    <div class="form-group col-sm-9 text-secondary">
                        <input id="f-link_url" type="text" name="link_url"  class="form-control" value="{{ $book->link_url }}"  />
                    </div>
                </div>
               
               
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="form-label mb-0" for="image">Photo</label>
                    </div>
                    <div class="form-group col-sm-9 text-secondary">
                        <input class="form-control" name="image" type="file" id="image" accept="image/*">
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">  </h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <img id="showImage" src="{{ asset($book->image) }}" alt="Preview" class="hms-preview">
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