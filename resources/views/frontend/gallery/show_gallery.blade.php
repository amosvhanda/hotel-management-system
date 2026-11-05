@extends('frontend.main_master')
@section('title', 'Gallery')
@section('main')

@php($site = App\Models\SiteSetting::current())
@include('frontend.body.page_head', [
    'kicker' => $site->home('gallery_kicker'),
    'title' => $site->home('gallery_title'),
])

<section class="hms-band hms-band--paper">
    <div class="container">
        @if ($gallery->isEmpty())
            <p class="hms-empty">Photographs will appear here once they are added.</p>
        @else
            <div class="hms-gallery gallery-view">
                @foreach ($gallery as $item)
                    <figure class="hms-gallery__item">
                        <a href="{{ asset($item->photo_name) }}?v=3" class="gallery-icon">
                            <img src="{{ asset($item->photo_name) }}?v=3" alt="{{ $item->caption ?: $site->name }}">
                        </a>
                        @if ($item->caption)
                            <figcaption>{{ $item->caption }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection
