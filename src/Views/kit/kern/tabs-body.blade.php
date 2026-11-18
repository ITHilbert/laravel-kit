{{-- Kern-Theme: data-kit-tabs-body bindet js/tabs.js (Name der Tab-Gruppe) --}}
<div {{ $attributes->merge(['data-kit-tabs-body' => ''])->class(['kit-tabs__body']) }}>{{ $slot }}</div>
