@php
    $site = $site ?? App\Models\SiteSetting::current();
    $testimonial = App\Models\Testimonial::latest()->limit(3)->get();
@endphp
@if ($testimonial->isNotEmpty())
<section class="hms-band hms-band--white">
    <div class="container">
        <div class="hms-section-head hms-section-head--left">
            <p class="hms-kicker hms-kicker--dark">{{ $site->home('guests_kicker') }}</p>
            <h2>{{ $site->home('guests_title') }}</h2>
        </div>
        <div class="hms-quotes">
            <figure class="hms-quotes__photo">
                <img src="{{ asset('frontend/assets/img/hotel/lobby.jpg') }}" alt="The hotel lobby">
            </figure>
            <div class="hms-quotes__list">
                @foreach ($testimonial as $item)
                    <blockquote class="hms-quote">
                        <p>{{ $item->message }}</p>
                        <footer>
                            @if ($item->image)<img src="{{ asset($item->image) }}" alt="">@endif
                            <span>
                                <strong>{{ $item->name }}</strong>
                                {{ $item->city }}
                            </span>
                        </footer>
                    </blockquote>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
