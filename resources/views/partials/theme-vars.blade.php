@php($__themeCss = App\Models\SiteSetting::current()->themeCss())
@if ($__themeCss)
<style id="hms-theme">{!! $__themeCss !!}</style>
@endif
