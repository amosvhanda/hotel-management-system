@extends('frontend.main_master')
@section('title', 'Rooms')
@section('main')

@php($site = App\Models\SiteSetting::current())
@include('frontend.body.page_head', [
    'kicker' => $site->home('rooms_page_kicker'),
    'title' => $site->home('rooms_page_title'),
])

<section class="hms-band hms-band--paper">
    <div class="container">
        @include('frontend.room._filters', [
            'action' => route('froom.all'),
            'resetUrl' => route('froom.all'),
            'showDates' => true,
            'count' => $rooms->count(),
            'dateError' => $dateError,
        ])

        <div class="hms-room-grid">
            @forelse ($rooms as $item)
                @include('frontend.room._tile', [
                    'room' => $item,
                    'url' => $stayQuery !== ''
                        ? route('search_room_details', $item->id).'?'.$stayQuery
                        : url('room/details/'.$item->id),
                    'left' => $item->rooms_left ?? null,
                ])
            @empty
                <div class="hms-empty">
                    @if ($checkIn !== '')
                        No rooms match those filters for {{ \Carbon\Carbon::parse($checkIn)->format('d M') }} to {{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}.
                    @else
                        No rooms match those filters.
                    @endif
                    <div class="mt-3"><a href="{{ route('froom.all') }}" class="hms-text-link">Clear filters</a></div>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
