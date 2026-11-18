{{-- Kern-Theme: Props wie bootstrap5/button-submit --}}
@props(['tooltip' => ''])
<button {{ $attributes->merge(['type' => 'submit', 'title' => $tooltip, 'data-variant' => 'submit'])->class(['kit-button']) }}>{{ $slot }}</button>
