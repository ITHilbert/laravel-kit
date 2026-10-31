{{-- Vorlage: vue/bootstrap5/show/show-date.vue (leerer Wert ohne Anzeige wie in vue/tailwind) --}}
@props(['value' => ''])
<div {{ $attributes->merge(['value' => $value, 'class' => 'show-date']) }}>
    {{ \ITHilbert\LaravelKit\Kit\KitAnzeige::datum($value) }}
</div>
