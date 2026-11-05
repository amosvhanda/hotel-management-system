@extends('frontend.main_master')
@section('main')

@php($site = App\Models\SiteSetting::current())
@include('frontend.body.page_head', [
    'kicker' => $site->home('rooms_page_kicker'),
    'title' => $site->home('rooms_page_title'),
])

<section class="hms-band hms-band--paper">
    <div class="container">
        <div class="hms-room-grid">
            @forelse ($rooms as $item)
                @include('frontend.room._tile', [
                    'room' => $item,
                    'url' => url('room/details/'.$item->id),
                ])
            @empty
                <p class="hms-empty">No rooms are listed yet.</p>
            @endforelse
        </div>
    </div>
</section>

@endsection
