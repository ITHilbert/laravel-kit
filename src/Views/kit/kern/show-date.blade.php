{{-- Kern-Theme: wie bootstrap5/show-date --}}
@props(['value' => ''])
<div {{ $attributes->merge(['value' => $value])->class(['kit-show-date']) }}>
    {{ \ITHilbert\LaravelKit\Kit\KitAnzeige::datum($value) }}
</div>
