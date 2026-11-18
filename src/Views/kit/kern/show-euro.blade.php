{{-- Kern-Theme: wie bootstrap5/show-euro --}}
@props(['value' => ''])
<div {{ $attributes->merge(['value' => $value])->class(['kit-show-euro']) }}>
    {{ \ITHilbert\LaravelKit\Kit\KitAnzeige::euro($value) }}
</div>
