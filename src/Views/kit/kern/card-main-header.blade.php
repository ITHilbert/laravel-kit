{{-- Kern-Theme: classheader hängt an kit-card__header (ohne Standardwert) --}}
@props(['classheader' => null])
<div {{ $attributes->class(['kit-card__header', $classheader]) }}>
    {{ $slot }}
</div>
