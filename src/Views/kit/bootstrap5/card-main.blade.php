{{-- Vorlage: vue/bootstrap5/card/card-main.vue --}}
@props(['classcard' => 'bg-light mb-3'])
<div {{ $attributes->class(['card card-main', $classcard]) }}>
    {{ $slot }}
</div>
