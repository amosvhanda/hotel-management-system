{{-- Published guest reviews for one room. Needs $roomdetails. --}}
@php
    $roomReviews = App\Models\Review::approved()->with('user', 'booking')->where('rooms_id', $roomdetails->id)->latest('approved_at')->get();
    $avg = $roomReviews->avg('rating');
@endphp
<div class="hms-reviews mt-4" id="reviews">
    <h3 class="mb-2">Guest reviews</h3>
    @if ($roomReviews->isEmpty())
        <p class="text-muted mb-0">No reviews yet. Guests can rate their stay from <em>My Bookings</em> after check-out.</p>
    @else
        <p class="hms-reviews__summary">
            <span class="hms-stars" aria-label="{{ number_format($avg, 1) }} out of 5">
                @for ($i = 1; $i <= 5; $i++)<i class="bx {{ $avg >= $i - 0.25 ? 'bxs-star' : ($avg >= $i - 0.75 ? 'bxs-star-half' : 'bx-star') }}"></i>@endfor
            </span>
            <strong>{{ number_format($avg, 1) }}</strong> / 5 · {{ $roomReviews->count() }} {{ Str::plural('review', $roomReviews->count()) }}
        </p>
        @foreach ($roomReviews->take(6) as $review)
            <article class="hms-review">
                <div class="hms-stars" aria-label="{{ $review->rating }} out of 5">
                    @for ($i = 1; $i <= 5; $i++)<i class="bx {{ $i <= $review->rating ? 'bxs-star' : 'bx-star' }}"></i>@endfor
                </div>
                @if ($review->title)<h4>{{ $review->title }}</h4>@endif
                <p>{{ $review->comment }}</p>
                <small class="text-muted">{{ $review->guestDisplayName() }} · stayed {{ \Carbon\Carbon::parse($review->booking->check_in ?? $review->created_at)->format('M Y') }}</small>
            </article>
        @endforeach
    @endif
</div>
<style>
    .hms-stars { color: #c9962c; letter-spacing: 1px; }
    .hms-reviews__summary { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .hms-review { border-top: 1px solid rgba(0,0,0,.08); padding: 16px 0; }
    .hms-review h4 { font-size: 1.05rem; margin: 6px 0 4px; }
    .hms-review p { margin: 6px 0; }
</style>
