@php
    $site = $site ?? App\Models\SiteSetting::current();
    $services = App\Models\Service::latest()->get();
@endphp

@if ($services->isNotEmpty())
<section class="hms-band hms-band--white" id="services">
    <div class="container">
        <div class="hms-section-head">
            <p class="hms-kicker hms-kicker--dark">{{ $site->home('services_kicker') }}</p>
            <h2>{{ $site->home('services_title') }}</h2>
        </div>

        <div class="row g-4">
            @foreach ($services as $service)
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('service.details', $service->id) }}" class="hms-service-card">
                        <div class="img" style="background-image:url('{{ $service->display_image }}')"></div>
                        <div class="body">
                            <span class="hms-service-card__icon"><i class="{{ $service->icon }}"></i></span>
                            <h3>{{ $service->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($service->description, 110) }}</p>
                            <span class="more">Learn more <i class="bx bx-right-arrow-alt"></i></span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
