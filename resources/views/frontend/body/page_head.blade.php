@php($site = $site ?? App\Models\SiteSetting::current())
<section class="hms-page-head {{ $class ?? '' }}">
    <div class="container">
        @if (!empty($kicker))
            <p class="hms-kicker hms-kicker--dark">{{ $kicker }}</p>
        @endif
        <h1>{{ $title }}</h1>
        @if (!empty($lead))
            <p class="hms-lead">{{ $lead }}</p>
        @endif
    </div>
</section>
