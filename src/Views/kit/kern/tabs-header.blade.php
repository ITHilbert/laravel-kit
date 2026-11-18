{{-- Kern-Theme: data-kit-tabs bindet js/tabs.js (Name der Tab-Gruppe) --}}
<div {{ $attributes->merge(['role' => 'tablist', 'data-kit-tabs' => ''])->class(['kit-tabs__list']) }}>{{ $slot }}</div>
