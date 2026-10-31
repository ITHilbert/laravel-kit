{{-- Vorlage: vue/tailwind/show/show-date.vue --}}
@props(['value' => ''])
<div {{ $attributes->merge(['value' => $value, 'class' => 'show-date']) }}>
    {{ \ITHilbert\LaravelKit\Kit\KitAnzeige::datum($value) }}
</div>
