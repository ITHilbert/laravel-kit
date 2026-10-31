{{-- Vorlage: vue/tailwind/tabs/tab-body.vue — sichtbar per active, Umschalten per js/tabs.js --}}
@props(['name' => null, 'active' => false])
<div {{ $attributes->merge(['id' => $name, 'role' => 'tabpanel', 'aria-labelledby' => $name.'-tab', 'data-kit-tab-body' => $name])->class(['tab-body', 'active' => $active, 'show' => $active]) }}>
    {{ $slot }}
</div>
