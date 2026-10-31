{{-- Vorlage: vue/bootstrap5/show/show-euro.vue (leerer Wert ohne Anzeige wie in vue/tailwind) --}}
@props(['value' => ''])
<div {{ $attributes->merge(['value' => $value, 'class' => 'show-euro']) }}>
    {{ \ITHilbert\LaravelKit\Kit\KitAnzeige::euro($value) }}
</div>
