@extends('frontend.main_master')
@section('title', 'Rate your stay')
@section('main')

@php($rating = (int) old('rating', 0))

@include('frontend.body.page_head', ['title' => 'Rate your stay', 'class' => 'hms-page-head--booking'])

<section class="pt-100 pb-70">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="hms-card">
                    <h3>How was your stay?</h3>
                    <p class="text-muted mb-4">
                        {{ $booking->room->type->name ?? 'Room' }} ·
                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }} – {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }} ·
                        Booking #{{ $booking->code }}
                    </p>

                    <form method="POST" action="{{ route('user.review.store', $booking->id) }}" novalidate>
                        @csrf

                        <fieldset class="mb-4">
                            <legend class="form-label fw-semibold fs-6">Your rating <span class="text-danger">*</span></legend>
                            <div class="hms-stars-input" role="radiogroup" aria-label="Star rating">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" @checked($rating === $i)>
                                    <label for="star{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}"><i class="bx bxs-star"></i><span class="visually-hidden">{{ $i }} stars</span></label>
                                @endfor
                            </div>
                            @error('rating')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </fieldset>

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Headline <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" id="title" name="title" maxlength="120" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="e.g. Quiet room and a great breakfast">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label fw-semibold">Your review <span class="text-danger">*</span></label>
                            <textarea id="comment" name="comment" rows="6" maxlength="2000" class="form-control @error('comment') is-invalid @enderror"
                                      placeholder="What did you enjoy? Anything we could do better?">{{ old('comment') }}</textarea>
                            @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Your first name and last initial are shown with your review once the hotel has checked it.</small>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="default-btn btn-bg-one border-radius-5">Send review</button>
                            <a href="{{ route('user.booking') }}" class="btn btn-outline-dark px-4">Back to my bookings</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .hms-stars-input { display: inline-flex; flex-direction: row-reverse; gap: 4px; }
    .hms-stars-input input { position: absolute; opacity: 0; width: 1px; height: 1px; }
    .hms-stars-input label { font-size: 34px; line-height: 1; cursor: pointer; margin: 0; }
    .hms-stars-input label i { color: #d6d3c7 !important; transition: color .12s ease; }
    .hms-stars-input label:hover i, .hms-stars-input label:hover ~ label i,
    .hms-stars-input input:checked ~ label i { color: #c9962c !important; }
    .hms-stars-input input:focus-visible + label { outline: 2px solid #1d3a2a; outline-offset: 2px; border-radius: 4px; }
</style>
@endsection
