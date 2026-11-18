{{-- Kern-Theme: classcard hängt an kit-card (ohne Standardwert) --}}
@props(['classcard' => null])
<div {{ $attributes->class(['kit-card', $classcard]) }}>
    {{ $slot }}
</div>
