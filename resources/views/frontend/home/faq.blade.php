@php
    $site = $site ?? App\Models\SiteSetting::current();
    $faqs = App\Models\Faq::orderBy('sort_order')->orderBy('id')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="hms-band hms-band--paper hms-faq" id="faq">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="faq-content">
                    <div class="hms-section-head hms-section-head--left">
                        <p class="hms-kicker hms-kicker--dark">{{ $site->home('faq_kicker') }}</p>
                        <h2>{{ $site->home('faq_title') }}</h2>
                        @if ($site->home('faq_intro'))
                            <p class="hms-lead">{{ $site->home('faq_intro') }}</p>
                        @endif
                    </div>

                    <div class="faq-accordion">
                        <ul class="accordion">
                            @foreach ($faqs as $faq)
                                <li class="accordion-item">
                                    <a class="accordion-title {{ $loop->first ? 'active' : '' }}" href="javascript:void(0)">
                                        <i class="bx bx-plus"></i>
                                        {{ $faq->question }}
                                    </a>
                                    <div class="accordion-content {{ $loop->first ? 'show' : '' }}">
                                        <p>{{ $faq->answer }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="faq-img-3">
                    <img src="{{ $site->faqImageUrl() }}" alt="{{ $site->name }}">
                </div>
            </div>
        </div>
    </div>
</section>
@endif
