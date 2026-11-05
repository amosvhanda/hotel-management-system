@php($current = old('icon_class', $service->icon_class ?? ''))
<div class="row mb-3">
    <div class="col-sm-3">
        <h6 class="mb-0">Icon</h6>
    </div>
    <div class="form-group col-sm-9 text-secondary">
        <select name="icon_class" class="form-control">
            <option value="">Choose an icon</option>
            @foreach (\App\Models\Service::ICON_OPTIONS as $class => $label)
                <option value="{{ $class }}" @selected($current === $class)>{{ $label }}</option>
            @endforeach
            @if ($current && ! array_key_exists($current, \App\Models\Service::ICON_OPTIONS))
                <option value="{{ $current }}" selected>{{ $current }}</option>
            @endif
        </select>
        <small class="text-muted">Shown on the service card on the homepage.</small>
    </div>
</div>
