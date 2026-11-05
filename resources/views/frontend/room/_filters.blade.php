{{--
    Room filter bar. A plain GET form: works without JavaScript, every state is a shareable URL.
    Needs: $action, $filters, $bounds, $options, $checkIn, $checkOut, $count, $resetUrl
    Optional: $showDates (true on /rooms), $dateError
--}}
@php
    $site = $site ?? App\Models\SiteSetting::current();
    $showDates = $showDates ?? false;
    $filtered = $filters['persion'] > ($showDates ? 0 : 1) || $filters['bed'] !== '' || $filters['view'] !== ''
        || count($filters['facility']) > 0 || $filters['sort'] !== '' || $filters['max_price'] < $bounds['max'] || $checkIn !== '';
@endphp
<form class="hms-filters" id="hmsFilters" method="get" action="{{ $action }}#results" aria-label="Filter rooms"
      data-money-sample="{{ $site->formatMoney(1000) }}">

    @if ($showDates)
        <div class="hms-filters__field">
            <label for="fIn">Check in</label>
            <input id="fIn" type="text" name="check_in" value="{{ $checkIn }}" class="dt_picker" autocomplete="off" placeholder="Any date">
        </div>
        <div class="hms-filters__field">
            <label for="fOut">Check out</label>
            <input id="fOut" type="text" name="check_out" value="{{ $checkOut }}" class="dt_picker" autocomplete="off" placeholder="Any date">
        </div>
    @else
        <input type="hidden" name="check_in" value="{{ $checkIn }}">
        <input type="hidden" name="check_out" value="{{ $checkOut }}">
    @endif

    <div class="hms-filters__field">
        <label for="fGuests">Guests</label>
        <select id="fGuests" name="persion">
            @if ($showDates)
                <option value="0" @selected($filters['persion'] === 0)>Any</option>
            @endif
            @for ($n = 1; $n <= $options['maxGuests']; $n++)
                <option value="{{ $n }}" @selected($filters['persion'] === $n)>{{ $n }} {{ $n === 1 ? 'guest' : 'guests' }}</option>
            @endfor
        </select>
    </div>

    @if ($options['beds']->count() > 1)
        <div class="hms-filters__field">
            <label for="fBed">Bed type</label>
            <select id="fBed" name="bed">
                <option value="">Any</option>
                @foreach ($options['beds'] as $bed)
                    <option value="{{ $bed }}" @selected($filters['bed'] === $bed)>{{ $bed }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if ($options['views']->count() > 1)
        <div class="hms-filters__field">
            <label for="fView">View</label>
            <select id="fView" name="view">
                <option value="">Any</option>
                @foreach ($options['views'] as $view)
                    <option value="{{ $view }}" @selected($filters['view'] === $view)>{{ $view }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if ($bounds['max'] > $bounds['min'])
        <div class="hms-filters__field hms-filters__field--range">
            <label for="fPrice">Price per night
                <output id="fPriceOut" for="fPrice">{{ $filters['max_price'] >= $bounds['max'] ? 'any' : 'up to '.$site->formatMoney($filters['max_price']) }}</output>
            </label>
            <input type="range" id="fPrice" name="max_price" min="{{ $bounds['min'] }}" max="{{ $bounds['max'] }}" step="1"
                   value="{{ $filters['max_price'] }}" data-max="{{ $bounds['max'] }}">
        </div>
    @endif

    <div class="hms-filters__field">
        <label for="fSort">Sort by</label>
        <select id="fSort" name="sort">
            <option value="" @selected($filters['sort'] === '')>Newest first</option>
            <option value="asc" @selected($filters['sort'] === 'asc')>Price, low to high</option>
            <option value="desc" @selected($filters['sort'] === 'desc')>Price, high to low</option>
        </select>
    </div>

    @if ($options['facilities']->isNotEmpty())
        <fieldset class="hms-filters__facilities">
            <legend>Must include</legend>
            @foreach ($options['facilities'] as $i => $facility)
                <label class="hms-chip">
                    <input type="checkbox" name="facility[]" value="{{ $facility }}" @checked(in_array(mb_strtolower($facility), array_map('mb_strtolower', $filters['facility']), true))>
                    <span>{{ $facility }}</span>
                </label>
            @endforeach
        </fieldset>
    @endif

    <div class="hms-filters__meta">
        <p id="results" role="status" aria-live="polite">
            <strong>{{ $count }}</strong> {{ $count === 1 ? 'room' : 'rooms' }}{{ $checkIn !== '' ? ' free for your dates' : '' }}
            @if ($dateError ?? null)<span class="hms-filters__error">{{ $dateError }}</span>@endif
        </p>
        <div class="hms-filters__actions">
            @if ($filtered)<a href="{{ $resetUrl }}" class="hms-filters__reset">Clear filters</a>@endif
            <button type="submit" class="hms-filters__apply">Show rooms</button>
        </div>
    </div>
</form>
