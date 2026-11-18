{{-- Kern-Theme: allgemeiner Button, variant steht als data-variant (Standard primary) --}}
@props(['variant' => 'primary'])
<button {{ $attributes->merge(['type' => 'button', 'data-variant' => $variant ?: 'primary'])->class(['kit-button']) }}>{{ $slot }}</button>
