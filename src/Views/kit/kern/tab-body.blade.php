{{-- Kern-Theme: Panel; ohne active steht hidden, js/tabs.js schaltet um --}}
@props(['name' => null, 'active' => false])
<div {{ $attributes->merge(['id' => $name, 'role' => 'tabpanel', 'aria-labelledby' => $name.'-tab', 'data-kit-tab-body' => $name])->class(['kit-tabs__panel', 'is-active' => $active]) }}@if (! $active) hidden @endif>{{ $slot }}</div>
