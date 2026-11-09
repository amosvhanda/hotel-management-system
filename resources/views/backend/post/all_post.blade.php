@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content"> 
	<!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
         
        <div class="breadcrumb-title pe-3">All Post</div>
<div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All Post</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('add.blog.post') }}" class="btn btn-primary px-5">Add Post </a>
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->


    
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table align-middle hms-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="hms-col-num">#</th>
                            <th>Post Title</th>
                            <th>Blog Category</th>
                            <th>Post Image</th> 
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($post as $key=> $item ) 
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $item->post_titile }}</td>
                            <td>{{ $item['blog']['category_name'] }}</td>
                            <td> <img src="{{ asset($item->post_image) }}" alt="" class="hms-thumb" > </td>
                             
                            <td>
    <a href="{{ route('edit.blog.post',$item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    <a href="{{ route('delete.blog.post',$item->id) }}" class="btn btn-sm btn-outline-danger" id="delete">Delete</a>

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