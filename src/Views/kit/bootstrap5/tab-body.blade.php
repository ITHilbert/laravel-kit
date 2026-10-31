{{-- Vorlage: vue/bootstrap5/tabs/tab-body.vue — Umschalten per js/tabs.js --}}
@props(['name' => null, 'active' => false])
<div {{ $attributes->merge(['id' => $name, 'role' => 'tabpanel', 'aria-labelledby' => $name.'-tab', 'data-kit-tab-body' => $name])->class(['tab-body tab-pane fade', 'show' => $active, 'active' => $active]) }}>{{ $slot }}</div>
