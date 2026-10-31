{{-- Vorlage: vue/bootstrap5/card/card-main-header.vue --}}
@props(['classheader' => 'font-weight-bold'])
<div {{ $attributes->class(['card-main-header', $classheader]) }}>
    {{ $slot }}
</div>
