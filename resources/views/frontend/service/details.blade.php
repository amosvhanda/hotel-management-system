@extends('frontend.main_master')
@section('title', $service->title)
@section('main')

@php($site = App\Models\SiteSetting::current())
@include('frontend.body.page_head', [
    'kicker' => $site->home('services_kicker'),
    'title' => $service->title,
])

<div class="service-details-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="service-article">
                    <div class="service-article-img mb-4">
                        <img src="{{ $service->display_image }}" alt="{{ $service->title }}" class="img-fluid rounded" style="width:100%;max-height:420px;object-fit:cover">
                    </div>
                    <div class="service-article-title">
                        <h2>
                            {{ $service->title }}
                        </h2>
                    </div>
                    <div class="service-article-content">
                        <p>{!! nl2br(e($service->description)) !!}</p>
                    </div>
                    <a href="{{ route('froom.all') }}" class="default-btn btn-bg-one border-radius-5 mt-3">Book a Room</a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="service-side-bar">
                    <div class="services-bar-widget">
                        <h3 class="title">Other Services</h3>
                        <div class="side-bar-categories">
                            <ul>
                                @forelse ($otherServices as $other)
                                    <li><a href="{{ route('service.details', $other->id) }}">{{ $other->title }}</a></li>
                                @empty
                                    <li class="text-muted">No other services yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
