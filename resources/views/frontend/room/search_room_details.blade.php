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
                        <h3>Booking Sheet </h3>

<form action="{{ route('user_booking_store',$roomdetails->id) }}" method="post" id="bk_form">
    @csrf
    
    <input type="hidden" name="room_id" value="{{ $roomdetails->id }}">


    <div class="row align-items-center">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Check in</label>
                <div class="input-group">
    <input autocomplete="off"  type="text" required name="check_in" id="check_in"  class="form-control dt_picker" value="{{ old('check_in') ? date('Y-m-d', strtotime(old('check_in'))) : '' }}" >
                    <span class="input-group-addon"></span>
                </div>
                <i class='bx bxs-calendar'></i>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Check Out</label>
                <div class="input-group">
   <input autocomplete="off"  type="text" required name="check_out" id="check_out"  class="form-control dt_picker" value="{{ old('check_out') ? date('Y-m-d', strtotime(old('check_out'))) : '' }}" >
                    <span class="input-group-addon"></span>
                </div>
                <i class='bx bxs-calendar'></i>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Numbers of Persons</label>
                @php($pickedGuests = (int) old('persion', request('persion', 1)))
                <select class="form-control" name="persion" id="nmbr_person">
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" @selected($pickedGuests === $i)>{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
                @endfor
                </select>
            </div>
        </div>

        {{-- Guests one room can sleep (capacity, else adults). --}}
        <input type="hidden" id="total_adult" value="{{ max(1, (int) ($roomdetails->room_capacity ?: $roomdetails->total_adult)) }}">
        <input type="hidden" id="room_price" value="{{ $roomdetails->price }}">
        <input type="hidden" id="discount_p" value="{{ $roomdetails->discount }}">

        <div class="col-lg-12">
            <div class="form-group">
                <label>Numbers of Rooms</label>
                @php($pickedRooms = (int) old('number_of_rooms', 1))
                <select class="form-control number_of_rooms" name="number_of_rooms" id="select_room">
                    @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" @selected($pickedRooms === $i)>{{ $i }} {{ $i === 1 ? 'room' : 'rooms' }}</option>
                    @endfor
                </select>
            </div>
            <input type="hidden" name="available_room" id="available_room" >
            <p class="available_room"></p>
            <p class="text-danger small mb-0" id="bk_error" role="alert" aria-live="polite"></p>
        </div>
     

        <div class="col-lg-12">
            <table class="table">
                
    <tbody>
        <tr> 
        <td><p> SubTotal</p></td>
        <td style="text-align: right" ><span class="t_subtotal">0</span> </td> 
        </tr>

        <tr> 
        <td><p> Discount</p></td>
        <td style="text-align: right" ><span class="t_discount">0</span></td> 
        </tr>

        <tr> 
        <td><p> Total</p></td>
        <td style="text-align: right" ><span class="t_g_total">0</span></td> 
        </tr>
        
    </tbody>
              </table>

        </div>






        <div class="col-lg-12 col-md-12">
            <button type="submit" class="default-btn btn-bg-three border-radius-5">
                Book Now
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

<script>
    $(document).ready(function () {
       var check_in = $("#check_in").val();
       var check_out = $("#check_out").val();
       var room_id = "{{ $room_id }}";
       if (check_in != '' && check_out != ''){
          getAvaility(check_in, check_out, room_id);
       }


       $("#check_in, #check_out").on('change', function () {
          var check_out = $("#check_out").val();
          var check_in = $("#check_in").val();

          if(check_in != '' && check_out != ''){
             getAvaility(check_in, check_out, room_id);
          }
       });

       $(".number_of_rooms").on('change', function () {
          var check_out = $("#check_out").val();
          var check_in = $("#check_in").val();

          if(check_in != '' && check_out != ''){
             getAvaility(check_in, check_out, room_id);
          }
       });


    });



    function getAvaility(check_in, check_out, room_id) {
       $.ajax({
          url: "{{ route('check_room_availability') }}",
          data: {room_id:room_id, check_in:check_in, check_out:check_out},
          error: function(xhr){
             var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not check these dates.';
             $(".available_room").html('');
             $("#available_room").val('');
             $("#bk_error").text(msg);
          },
          success: function(data){
             var n = parseInt(data['available_room']);
             $(".available_room").html(n > 0
                ? 'Available: <span class="text-success">'+n+(n === 1 ? ' room' : ' rooms')+'</span>'
                : '<span class="text-danger">Fully booked for these dates. Try other dates.</span>');
             $("#bk_error").text('');
             $("#available_room").val(data['available_room']);
             price_calculate(data['total_nights']);
          }
       });
    }

    function price_calculate(total_nights){
       var room_price = $("#room_price").val();
       var discount_p = $("#discount_p").val();
       var select_room = $("#select_room").val();

       var sub_total = room_price * total_nights * parseInt(select_room);

       var discount_price = (parseInt(discount_p)/100)*sub_total;

       $(".t_subtotal").text(sub_total.toFixed(2));
       $(".t_discount").text(discount_price.toFixed(2));
       $(".t_g_total").text((sub_total-discount_price).toFixed(2));

    }

    $("#bk_form").on('submit', function () {
       var av = $("#available_room").val();
       var rooms = parseInt($("#select_room").val());
       var guests = parseInt($("#nmbr_person").val());
       var perRoom = parseInt($("#total_adult").val());
       $("#bk_error").text('');
       if (av !== '' && rooms > parseInt(av)) {
          $("#bk_error").text(parseInt(av) > 0
             ? 'Only ' + av + ' room(s) are free for these dates. Choose fewer rooms.'
             : 'This room is fully booked for these dates. Try other dates.');
          return false;
       }
       if (guests > perRoom * rooms) {
          $("#bk_error").text('One room sleeps up to ' + perRoom + ' guest(s). Add another room for ' + guests + ' guests.');
          return false;
       }
    });
    $("#nmbr_person, #select_room").on('change', function () { $("#bk_error").text(''); });
 </script>


@endsection