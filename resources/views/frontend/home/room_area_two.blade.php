@php
    $bookarea = App\Models\BookArea::find(1);
@endphp
@if ($bookarea)
<section class="hms-band hms-invite">
    <div class="container">
        <div class="hms-invite__grid">
            <div class="hms-invite__copy">
                @if ($bookarea->short_title)
                    <p class="hms-kicker">{{ $bookarea->short_title }}</p>
                @endif
                <h2>{{ $bookarea->main_title }}</h2>
                @if ($bookarea->short_desc)
                    <p>{{ $bookarea->short_desc }}</p>
                @endif
                <a href="{{ $bookarea->link_url ?: route('froom.all') }}" class="hms-invite__btn">See the rooms</a>
            </div>
            <div class="hms-invite__photo">
                <img src="{{ asset($bookarea->image) }}?v=3" alt="{{ $bookarea->main_title }}">
            </div>
        </div>
    </div>
</section>
@endif
