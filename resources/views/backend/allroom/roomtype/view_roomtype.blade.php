@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content"> 
	<!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
         
        <div class="breadcrumb-title pe-3">Room Type List</div>
<div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Room Type List</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('add.room.type') }}" class="btn btn-primary px-5"> Add Room Type </a>
                
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
                            <th style="width:56px">#</th>
                            <th>Image</th>
                            <th>Name</th> 
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($allData as $key=> $item ) 
        @php
            $rooms = App\Models\Room::where('roomtype_id',$item->id)->get();
        @endphp

                        <tr>
    <td>{{ $key+1 }}</td>
    <td> <img src="{{ (!empty($item->room->image)) ? url('upload/roomimg/'.$item->room->image) : url('upload/no_image.jpg') }}" alt="" class="hms-thumb" >   </td>
                            <td>{{ $item->name }}</td> 
                            <td>

     @foreach ($rooms as $roo) 
    <a href="{{ route('edit.room',$roo->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    <a href="{{ route('delete.room',$roo->id) }}" class="btn btn-sm btn-outline-danger" id="delete">Delete</a>
    @endforeach  
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