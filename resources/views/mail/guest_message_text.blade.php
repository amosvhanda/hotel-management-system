{!! $bodyText !!}

@if ($buttonText && $buttonUrl){{ $buttonText }}: {{ $buttonUrl }}
@endif

-- 
{{ App\Models\SiteSetting::current()->name }} · {{ App\Models\SiteSetting::current()->phone }}
