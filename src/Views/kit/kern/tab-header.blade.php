{{-- Kern-Theme: Reiter; Umschalten per js/tabs.js --}}
@props(['name' => null, 'active' => false, 'addClass' => null])
<button {{ $attributes->merge(['id' => $name.'-tab', 'type' => 'button', 'role' => 'tab', 'aria-controls' => $name, 'aria-selected' => $active ? 'true' : 'false', 'data-kit-tab' => $name])->class(['kit-tabs__tab', $addClass, 'is-active' => $active]) }}>{{ $slot }}</button>
