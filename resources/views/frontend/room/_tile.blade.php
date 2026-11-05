@php($site = $site ?? App\Models\SiteSetting::current())
<article class="hms-room-tile">
    <a class="hms-room-tile__photo" href="{{ $url }}">
        <img src="{{ asset('upload/roomimg/'.$room->image) }}?v=3" alt="{{ $room->type->name ?? 'Room' }}">
    </a>
    <div class="hms-room-tile__body">
        <h3><a href="{{ $url }}">{{ $room->type->name ?? 'Room' }}</a></h3>
        <p class="hms-stay__price">{{ $site->formatMoney($room->price) }} <small>/ night</small></p>
        @if ($room->short_desc)
            <p>{{ \Illuminate\Support\Str::limit($room->short_desc, 110) }}</p>
        @endif
        <a class="hms-text-link" href="{{ $url }}">View room</a>
    </div>
</article>
