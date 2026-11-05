@extends('admin.admin_dashboard')
@section('title', 'Brand colours')
@section('admin')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Brand colours</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item">Setting</li>
                    <li class="breadcrumb-item active" aria-current="page">Brand colours</li>
                </ol>
            </nav>
        </div>
    </div>

    @unless ($ready)
        <div class="alert alert-warning">One setup step is missing. Run <code>php artisan migrate</code> in the project folder, then reload this page.</div>
    @endunless

    @php
        $fields = [
            'Brand' => [
                'forest' => ['Deep green', 'Sidebar, footer and dark sections'],
                'canopy' => ['Main green', 'Buttons, links and headings'],
                'leaf'   => ['Light green', 'Button hover, charts and focus rings'],
                'sand'   => ['Accent', 'Highlights, active menu marker and badges'],
            ],
            'Surfaces and text' => [
                'paper' => ['Page background', 'Behind every page'],
                'card'  => ['Cards and panels', 'Forms, tables and boxes'],
                'ink'   => ['Text', 'Headings and body copy'],
                'muted' => ['Soft text', 'Captions and helper text'],
            ],
        ];
        $presets = [
            'Safari lodge (original)' => ['forest' => '#14241b', 'canopy' => '#1f3a2b', 'leaf' => '#3f6b4a', 'sand' => '#c9b88a', 'paper' => '#f4f3ec', 'card' => '#fbfbf6', 'ink' => '#15201a', 'muted' => '#5d695f'],
            'Lake blue'               => ['forest' => '#10222e', 'canopy' => '#1b4560', 'leaf' => '#2f6f8f', 'sand' => '#d4b98a', 'paper' => '#f2f5f6', 'card' => '#fafcfc', 'ink' => '#14222b', 'muted' => '#56666f'],
            'Terracotta dusk'         => ['forest' => '#2a1a14', 'canopy' => '#7a3b22', 'leaf' => '#a0522d', 'sand' => '#d9b779', 'paper' => '#f7f1ea', 'card' => '#fdf9f4', 'ink' => '#26180f', 'muted' => '#6e5d52'],
            'Charcoal and gold'       => ['forest' => '#1a1a1a', 'canopy' => '#2b2b2b', 'leaf' => '#5a5a52', 'sand' => '#c8a64a', 'paper' => '#f5f4f0', 'card' => '#fcfbf8', 'ink' => '#1c1c1a', 'muted' => '#62625c'],
        ];
    @endphp

    <form method="POST" action="{{ route('theme.update') }}" id="themeForm" novalidate>
        @csrf
        <div class="row g-3">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-body p-4">
                        <p class="text-secondary">Change these once and the public website, the sign-in pages and this admin all follow. Status colours (paid, pending, unpaid) stay fixed so they keep their meaning.</p>

                        <div class="mb-4">
                            <label class="form-label d-block">Start from a palette</label>
                            <div class="d-flex flex-wrap gap-2" id="presets">
                                @foreach ($presets as $name => $p)
                                    <button type="button" class="btn btn-sm btn-light hms-preset" data-colors='@json($p)'>
                                        <span class="hms-swatches">@foreach (['canopy','leaf','sand','paper'] as $k)<i style="background: {{ $p[$k] }}"></i>@endforeach</span>
                                        {{ $name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @foreach ($fields as $group => $items)
                            <h6 class="mt-4 mb-3">{{ $group }}</h6>
                            <div class="row g-3">
                                @foreach ($items as $key => [$label, $help])
                                    <div class="col-md-6">
                                        <label class="form-label" for="c-{{ $key }}-hex">{{ $label }}</label>
                                        <div class="hms-color">
                                            <input type="color" id="c-{{ $key }}" value="{{ old($key, $colors[$key]) }}" aria-label="{{ $label }} colour picker" data-key="{{ $key }}">
                                            <input type="text" name="{{ $key }}" id="c-{{ $key }}-hex" value="{{ old($key, $colors[$key]) }}" maxlength="7" class="form-control @error($key) is-invalid @enderror" data-default="{{ $defaults[$key] }}" autocomplete="off" spellcheck="false">
                                        </div>
                                        <div class="form-text">{{ $help }}</div>
                                        @error($key)<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <div id="contrast" class="mt-4" aria-live="polite"></div>

                        <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4" @disabled(! $ready)>Save colours</button>
                            <button type="submit" class="btn btn-outline-secondary" form="themeReset">Reset to original</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card hms-preview-card">
                    <div class="card-header"><h6 class="mb-0">Preview</h6></div>
                    <div class="card-body">
                        <div class="hms-pv" id="pv">
                            <div class="hms-pv-side">
                                <b>{{ $site->name }}</b>
                                <span class="on">Dashboard</span><span>Bookings</span><span>Rooms</span>
                            </div>
                            <div class="hms-pv-main">
                                <h5>Good morning</h5>
                                <p>Three guests arrive today.</p>
                                <div class="hms-pv-card">
                                    <small>Occupancy tonight</small>
                                    <strong>72%</strong>
                                    <div class="hms-pv-bar"><i></i></div>
                                </div>
                                <div class="hms-pv-actions">
                                    <span class="hms-pv-btn">New booking</span>
                                    <span class="hms-pv-link">View all</span>
                                    <span class="hms-pv-chip">Sand accent</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-secondary small mt-3 mb-0">The preview updates as you pick. Nothing changes on the site until you save.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form method="POST" action="{{ route('theme.reset') }}" id="themeReset">@csrf</form>
</div>

@push('scripts')
<style>
    .hms-color { display: flex; gap: 8px; align-items: center; }
    .hms-color input[type=color] { width: 48px; height: 42px; padding: 3px; border: 1px solid var(--hms-line); border-radius: 6px; background: var(--hms-field); cursor: pointer; flex: none; }
    .hms-color .form-control { font-family: ui-monospace, Consolas, monospace; text-transform: lowercase; }
    .hms-swatches { display: inline-flex; margin-right: 6px; vertical-align: middle; }
    .hms-swatches i { width: 14px; height: 14px; border: 1px solid rgba(0,0,0,.15); margin-left: -3px; border-radius: 50%; display: block; }
    .hms-swatches i:first-child { margin-left: 0; }
    .hms-pv { --f:#14241b; --c:#1f3a2b; --l:#3f6b4a; --s:#c9b88a; --p:#f4f3ec; --k:#fbfbf6; --i:#15201a; --m:#5d695f; display: grid; grid-template-columns: 120px 1fr; border: 1px solid var(--hms-line); border-radius: 8px; overflow: hidden; min-height: 300px; background: var(--p); }
    .hms-pv-side { background: var(--f); padding: 16px 12px; display: flex; flex-direction: column; gap: 6px; font-size: 12.5px; color: #cfd8ca; }
    .hms-pv-side b { color: #f4f3ec; font-family: var(--hms-display); font-weight: 400; font-size: 15px; margin-bottom: 10px; }
    .hms-pv-side span { padding: 6px 8px; border-left: 3px solid transparent; }
    .hms-pv-side span.on { border-left-color: var(--s); background: rgba(255,255,255,.07); color: #fff; }
    .hms-pv-main { padding: 18px; color: var(--i); }
    .hms-pv-main h5 { font-family: var(--hms-display); font-weight: 400; font-size: 22px; margin: 0 0 4px; color: var(--i); }
    .hms-pv-main p { color: var(--m); font-size: 13px; margin: 0 0 14px; }
    .hms-pv-card { background: var(--k); border: 1px solid rgba(0,0,0,.09); border-radius: 8px; padding: 12px 14px; margin-bottom: 14px; }
    .hms-pv-card small { color: var(--m); display: block; font-size: 12px; }
    .hms-pv-card strong { font-family: var(--hms-display); font-weight: 300; font-size: 30px; line-height: 1.1; }
    .hms-pv-bar { height: 6px; background: rgba(0,0,0,.07); border-radius: 99px; margin-top: 6px; overflow: hidden; }
    .hms-pv-bar i { display: block; width: 72%; height: 100%; background: var(--l); }
    .hms-pv-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
    .hms-pv-btn { background: var(--c); color: #f4f3ec; padding: 7px 14px; border-radius: 6px; font-size: 13px; font-weight: 500; }
    .hms-pv-link { color: var(--c); font-size: 13px; text-decoration: underline; text-underline-offset: 3px; }
    .hms-pv-chip { background: var(--s); color: var(--f); padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 600; }
</style>
<script>
(function () {
    var keys = ['forest','canopy','leaf','sand','paper','card','ink','muted'];
    var map = { forest:'--f', canopy:'--c', leaf:'--l', sand:'--s', paper:'--p', card:'--k', ink:'--i', muted:'--m' };
    var pv = document.getElementById('pv'), note = document.getElementById('contrast');
    var hexRe = /^#[0-9a-f]{6}$/i;
    function val(k) { var v = document.getElementById('c-' + k + '-hex').value.trim(); return hexRe.test(v) ? v : null; }
    function lum(h) { var n = parseInt(h.slice(1), 16), a = [n >> 16 & 255, n >> 8 & 255, n & 255].map(function (x) { x /= 255; return x <= .03928 ? x / 12.92 : Math.pow((x + .055) / 1.055, 2.4); }); return .2126 * a[0] + .7152 * a[1] + .0722 * a[2]; }
    function ratio(a, b) { var x = lum(a), y = lum(b); return (Math.max(x, y) + .05) / (Math.min(x, y) + .05); }
    function paint() {
        keys.forEach(function (k) { var v = val(k); if (v) { pv.style.setProperty(map[k], v); document.getElementById('c-' + k).value = v; } });
        var checks = [['Text on the page background', 'ink', 'paper'], ['Soft text on the page background', 'muted', 'paper'], ['Button text on the main green', null, 'canopy', '#f4f3ec']], msgs = [];
        checks.forEach(function (c) {
            var fg = c[3] || val(c[1]), bg = val(c[2]);
            if (!fg || !bg) return;
            var r = ratio(fg, bg);
            if (r < 4.5) msgs.push('<li>' + c[0] + ' is hard to read (contrast ' + r.toFixed(1) + ':1, aim for 4.5:1 or more).</li>');
        });
        note.innerHTML = msgs.length ? '<div class="alert alert-warning mb-0"><strong>Check readability</strong><ul class="mb-0 mt-1">' + msgs.join('') + '</ul></div>' : '';
    }
    keys.forEach(function (k) {
        var pick = document.getElementById('c-' + k), hex = document.getElementById('c-' + k + '-hex');
        pick.addEventListener('input', function () { hex.value = pick.value; paint(); });
        hex.addEventListener('input', function () { if (hex.value && hex.value[0] !== '#') hex.value = '#' + hex.value; paint(); });
    });
    document.querySelectorAll('.hms-preset').forEach(function (b) {
        b.addEventListener('click', function () {
            var c = JSON.parse(b.getAttribute('data-colors'));
            keys.forEach(function (k) { document.getElementById('c-' + k + '-hex').value = c[k]; });
            paint();
        });
    });
    paint();
})();
</script>
@endpush
@endsection
