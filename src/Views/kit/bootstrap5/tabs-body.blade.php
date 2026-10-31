{{-- Vorlage: vue/bootstrap5/tabs/tabs-body.vue — data-kit-tabs-body: Name der Tab-Gruppe (js/tabs.js) --}}
<div {{ $attributes->merge(['class' => 'tab-content tabs-body', 'data-kit-tabs-body' => '']) }}>{{ $slot }}</div>
