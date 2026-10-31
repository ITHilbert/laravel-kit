{{-- Vorlage: vue/tailwind/card/card-main.vue --}}
<div {{ $attributes->merge(['class' => 'card-main']) }}>
    {{ $slot }}
</div>
