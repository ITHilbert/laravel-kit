{{-- Vorlage: vue/bootstrap5/tabs/tabs-header.vue — data-kit-tabs: Name der Tab-Gruppe (js/tabs.js) --}}
<ul {{ $attributes->merge(['class' => 'nav nav-tabs tabs-header', 'role' => 'tablist', 'data-kit-tabs' => '']) }}>{{ $slot }}</ul>
