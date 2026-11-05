@php
    $site = $site ?? App\Models\SiteSetting::current();
    $room = App\Models\Room::latest()->limit(4)->get();
@endphp
@if ($room->isNotEmpty())
<section class="hms-band hms-band--paper" id="rooms">
    <div class="container">
        <div class="hms-section-head">
            <p class="hms-kicker hms-kicker--dark">{{ $site->home('rooms_kicker') }}</p>
            <h2>{{ $site->home('rooms_title') }}</h2>
        </div>
        <div class="hms-stays">
            @foreach ($room as $item)
                <article class="hms-stay">
                    <a class="hms-stay__photo" href="{{ url('room/details/'.$item->id) }}">
                        <img src="{{ asset('upload/roomimg/'.$item->image) }}?v=3" alt="{{ $item['type']['name'] }}">
                    </a>
                    <div class="hms-stay__body">
                        <div class="hms-stay__top">
                            <h3><a href="{{ url('room/details/'.$item->id) }}">{{ $item['type']['name'] }}</a></h3>
                            <p class="hms-stay__price">{{ $site->formatMoney($item->price) }} <small>/ night</small></p>
                        </div>
                        <p class="hms-stay__desc">{{ $item->short_desc }}</p>
                        <ul class="hms-stay__facts">
                            <li><i class="bx bx-user"></i> {{ $item->room_capacity }} {{ $item->room_capacity == 1 ? 'guest' : 'guests' }}</li>
                            <li><i class="bx bx-expand"></i> {{ $item->size }} ft²</li>
                            <li><i class="bx bx-show-alt"></i> {{ $item->view }}</li>
                            <li><i class="bx bx-bed"></i> {{ $item->bed_style }}</li>
                        </ul>
                        <a class="hms-text-link" href="{{ url('room/details/'.$item->id) }}">View room</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
