{{-- Vorlage: vue/bootstrap5/tabs/tab-header.vue — Umschalten per js/tabs.js --}}
@props(['name' => null, 'active' => false, 'addClass' => ''])
<li class="nav-item" role="presentation">
    <button {{ $attributes->merge(['id' => $name.'-tab', 'data-bs-toggle' => 'tab', 'data-bs-target' => '#'.$name, 'type' => 'button', 'role' => 'tab', 'aria-controls' => $name, 'aria-selected' => $active ? 'true' : 'false', 'data-kit-tab' => $name])->class(['tab-header nav-link', 'active' => $active, $addClass]) }}>{{ $slot }}</button>
</li>
