{{-- Vorlage: vue/bootstrap5/card/card-body.vue --}}
<div {{ $attributes->merge(['class' => 'card-body']) }}>
    {{ $slot }}
</div>
