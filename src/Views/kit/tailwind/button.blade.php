{{-- Allgemeiner Button: variant ergibt die Klasse btn-<variant> (Standard primary) --}}
@props(['variant' => 'primary'])
<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-'.($variant ?: 'primary')]) }}>{{ $slot }}</button>
