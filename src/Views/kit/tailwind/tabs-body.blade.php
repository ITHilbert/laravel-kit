{{-- Vorlage: vue/tailwind/tabs/tabs-body.vue — data-kit-tabs-body: Name der Tab-Gruppe (js/tabs.js) --}}
<div {{ $attributes->merge(['class' => 'tabs-body', 'data-kit-tabs-body' => '']) }}>{{ $slot }}</div>
