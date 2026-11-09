@extends('frontend.main_master')
@section('main')
@php($site = App\Models\SiteSetting::current())
@include('frontend.body.page_head', [
    'kicker' => $site->home('rooms_page_kicker'),
    'title' => $roomdetails->type->name,
])

<!-- Room Details Area End -->
<div class="room-details-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="room-details-side">
                    <div class="side-bar-form">
                        <h3>Check these dates</h3>
                        <form method="get" action="{{ route('booking.search') }}">
                            <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Check in</label>
                                        <input autocomplete="off" type="text" required name="check_in" class="form-control dt_picker" placeholder="Select a date">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Check out</label>
                                        <input autocomplete="off" type="text" required name="check_out" class="form-control dt_picker" placeholder="Select a date">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Guests</label>
                                        <select name="persion" class="form-control">
                                            <option value="1">1 guest</option>
                                            <option value="2">2 guests</option>
                                            <option value="3">3 guests</option>
                                            <option value="4">4 guests</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12">
                                    <button type="submit" class="default-btn btn-bg-three border-radius-5">
                                        Check availability
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                  
                </div>
            </div>

            <div class="col-lg-8">
                <div class="room-details-article">
                    
                    <div class="room-details-slider owl-carousel owl-theme">
                        @foreach ($multiImage as $image) 
                        <div class="room-details-item">
                            <img src="{{ asset('upload/roomimg/multi_img/'.$image->multi_img) }}?v=3" alt="{{ $roomdetails->type->name }}">
                        </div>
                        @endforeach
                       
                    </div>





                    <div class="room-details-title">
                        <h2>{{ $roomdetails->type->name }}</h2>
                        <ul>
                            
                            <li>
                               <b> Basic : {{ \App\Models\SiteSetting::current()->formatMoney($roomdetails->price) }}/Night/Room</b>
                            </li> 
                         
                        </ul>
                    </div>

                    <div class="room-details-content">
                        <p>
                            {!! $roomdetails->description !!}
                        </p>




<ul class="hms-stay__facts">
                        @foreach ($facility as $fac)
                            <li><i class="bx bx-check"></i> {{ $fac->facility_name }}</li>
                        @endforeach
                        <li><i class="bx bx-user"></i> {{ $roomdetails->room_capacity }} {{ $roomdetails->room_capacity == 1 ? 'guest' : 'guests' }}</li>
                        <li><i class="bx bx-expand"></i> {{ $roomdetails->size }} ft²</li>
                        <li><i class="bx bx-show-alt"></i> {{ $roomdetails->view }}</li>
                        <li><i class="bx bx-bed"></i> {{ $roomdetails->bed_style }}</li>
                    </ul>

                    </div>

                    @include('frontend.room._reviews')

                    <div class="hms-room-note">
                        <h2>{{ $site->home('about_cta_title') }}</h2>
                        <p>{{ $site->home('booking_note') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Room Details Area End -->

<!-- Room Details Other -->
<div class="room-details-other pb-70">
    <div class="container">
        <div class="room-details-text">
            <h2>Other Rooms </h2>
        </div>

        <div class="row ">
           
           @foreach ($otherRooms as $item)
            <div class="col-lg-6">
                <div class="room-card-two">
                    <div class="row align-items-center">
                        <div class="col-lg-5 col-md-4 p-0">
                            <div class="room-card-img">
                                <a href="{{ url('room/details/'.$item->id) }}">
                                    <img src="{{ asset('upload/roomimg/'.$item->image) }}?v=3" alt="{{ $item['type']['name'] }}">
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-8 p-0">
                            <div class="room-card-content">
                                 <h3>
             <a href="{{ url('room/details/'.$item->id) }}">{{ $item['type']['name'] }}</a>
                                </h3>
                                <span class="hms-rate">{{ $site->formatMoney($item->price) }} <small>/ night</small></span>
                                <p>{{ $item->short_desc }}</p>
                                <ul>
                   <li><i class='bx bx-user'></i> {{ $item->room_capacity }} Person</li>
                   <li><i class='bx bx-expand'></i> {{ $item->size }}ft2</li>
                                </ul>

                                <ul>
        <li><i class='bx bx-show-alt'></i>{{ $item->view }}</li>
        <li><i class='bx bxs-hotel'></i> {{ $item->bed_style }}</li>
                                </ul>
                                
                                <a href="{{ url('room/details/'.$item->id) }}" class="book-more-btn">
                                    View room
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            @endforeach
           


        </div>
    </div>
</div>
<!-- Room Details Other End -->




@endsection