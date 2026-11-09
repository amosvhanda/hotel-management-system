{{-- SEO + link previews (WhatsApp / Facebook / X) + Google hotel data. Pages can set:
     @section('title'), @section('meta_description'), @section('og_image'), @push('jsonld') --}}
@php
    $seoTitle = trim(View::yieldContent('title')) ? trim(View::yieldContent('title')) . ' | ' . ($site->meta_title ?: $site->name) : ($site->meta_title ?: $site->name);
    $seoDesc = trim(View::yieldContent('meta_description')) ?: ($site->meta_description ?: ($site->tagline ?: 'Book your stay at ' . $site->name));
    $seoImage = trim(View::yieldContent('og_image')) ?: $site->heroImageUrl();
    $seoUrl = url()->current();
@endphp
<link rel="canonical" href="{{ $seoUrl }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $site->name }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($seoDesc), 200) }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($seoDesc), 200) }}">
<meta name="twitter:image" content="{{ $seoImage }}">
@if ($site->google_site_verification)<meta name="google-site-verification" content="{{ $site->google_site_verification }}">@endif
@if (request()->routeIs('home'))
@php
    $allApproved = App\Models\Review::approved();
    $hotelLd = array_filter([
        '@context' => 'https://schema.org', '@type' => 'Hotel',
        'name' => $site->name, 'url' => url('/'), 'image' => $site->heroImageUrl(), 'logo' => $site->logo_url,
        'description' => $site->site_description ?: $site->tagline,
        'telephone' => $site->phone, 'email' => $site->email,
        'address' => $site->address ? ['@type' => 'PostalAddress', 'streetAddress' => $site->address, 'addressCountry' => 'ZW'] : null,
        'priceRange' => ($min = App\Models\Room::where('status', 1)->min(DB::raw('CAST(price AS DECIMAL(10,2))'))) ? 'From ' . $site->formatMoney($min, 0) . ' per night' : null,
        'aggregateRating' => ($cnt = (clone $allApproved)->count()) ? ['@type' => 'AggregateRating', 'ratingValue' => round((clone $allApproved)->avg('rating'), 1), 'reviewCount' => $cnt, 'bestRating' => 5] : null,
    ]);
@endphp
<script type="application/ld+json">{!! json_encode($hotelLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@stack('jsonld')
