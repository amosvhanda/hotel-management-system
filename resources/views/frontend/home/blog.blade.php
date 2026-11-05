@php
    $site = $site ?? App\Models\SiteSetting::current();
    $blog = App\Models\BlogPost::latest()->limit(3)->get();
@endphp
@if ($blog->isNotEmpty())
<section class="hms-band hms-band--white hms-journal">
    <div class="container">
        <div class="hms-section-head">
            <p class="hms-kicker hms-kicker--dark">{{ $site->home('journal_kicker') }}</p>
            <h2>{{ $site->home('journal_title') }}</h2>
        </div>
        <div class="row g-4">
            @foreach ($blog as $item)
            <div class="col-lg-4 col-md-6">
                <div class="blog-item">
                    <a href="{{ url('blog/details/'.$item->post_slug) }}">
                        <img src="{{ asset($item->post_image) }}" alt="{{ $item->post_titile }}">
                    </a>
                    <div class="content">
                        <ul>
                            <li>{{ $item->created_at->format('M d, Y') }}</li>
                        </ul>
                        <h3>
                            <a href="{{ url('blog/details/'.$item->post_slug) }}">{{ $item->post_titile }}</a>
                        </h3>
                        <p>{{ $item->short_descp }}</p>
                        <a href="{{ url('blog/details/'.$item->post_slug) }}" class="hms-text-link">Read more</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
