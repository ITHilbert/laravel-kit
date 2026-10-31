{{-- Vorlage: vue/tailwind/tabs/tabs-header.vue — data-kit-tabs: Name der Tab-Gruppe (js/tabs.js) --}}
<ul {{ $attributes->merge(['class' => 'tabs-header', 'role' => 'tablist', 'data-kit-tabs' => '']) }}>{{ $slot }}</ul>
