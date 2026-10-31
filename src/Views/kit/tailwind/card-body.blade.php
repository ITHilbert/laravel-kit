{{-- Vorlage: vue/tailwind/card/card-body.vue --}}
<div {{ $attributes->merge(['class' => 'card-body']) }}>
    {{ $slot }}
</div>
