{{-- Vorlage: vue/tailwind/show/show-euro.vue --}}
@props(['value' => ''])
<div {{ $attributes->merge(['value' => $value, 'class' => 'show-euro']) }}>
    {{ \ITHilbert\LaravelKit\Kit\KitAnzeige::euro($value) }}
</div>
