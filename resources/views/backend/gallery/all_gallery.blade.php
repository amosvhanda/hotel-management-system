@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content"> 
	<!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
         
        <div class="breadcrumb-title pe-3">All Gallery Photo</div>
<div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All Gallery Photo</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('add.gallery') }}" class="btn btn-primary px-5">Add Gallery </a>
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->


    
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
               
   <form action="{{ route('delete.gallery.multiple') }}" method="POST">
    @csrf
           
                <table class="table align-middle hms-table" style="width:100%">
                    <thead>
                        <tr>
                            <th width="50px">Select</th>
                            <th class="hms-col-num">#</th>
                            <th>Image</th>
                            <th>Caption</th>
                            <th>Homepage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($gallery as $key=> $item ) 
                        <tr> 
    <td>
        <input type="checkbox" name="selectedItem[]" value="{{ $item->id }}">
        </td>
                            <td>{{ $key+1 }}</td>
                            <td> <img src="{{ asset($item->photo_name) }}" alt="" class="hms-thumb" > </td>
                            <td>{{ $item->caption }}</td>
                            <td>{{ $item->show_on_home ? 'Yes' : '' }}</td>
                            <td>
    <a href="{{ route('edit.gallery',$item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    <a href="{{ route('delete.gallery',$item->id) }}" class="btn btn-sm btn-outline-danger" id="delete">Delete</a>

                            </td>
                        </tr>
                        @endforeach 
                      
                    </tbody>
                 
                </table>
        <button type="submit" class="btn btn-outline-danger">Delete selected</button>
            </form>     
            </div>
        </div>
    </div>
     
    <hr/>
     
</div>




@endsection