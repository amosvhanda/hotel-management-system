@php
    $site = $site ?? App\Models\SiteSetting::current();
    $team = App\Models\Team::latest()->get();
@endphp

@if ($team->isNotEmpty())
<section class="hms-band hms-band--paper">
    <div class="container">
        <div class="hms-section-head">
            <p class="hms-kicker hms-kicker--dark">{{ $site->home('team_kicker') }}</p>
            <h2>{{ $site->home('team_title') }}</h2>
        </div>
        <div class="hms-people">
            @foreach ($team as $item)
                <article class="hms-person">
                    <a href="{{ route('about') }}">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}">
                    </a>
                    <h3><a href="{{ route('about') }}">{{ $item->name }}</a></h3>
                    <p>{{ $item->postion }}</p>
                    @if (filled($item->facebook) && $item->facebook !== '#')
                        <a class="hms-person__social" href="{{ $item->facebook }}" target="_blank" rel="noopener" aria-label="{{ $item->name }} on Facebook"><i class="bx bxl-facebook"></i></a>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
