{{-- Kern-Theme: Props wie bootstrap5/button-save --}}
@props(['route' => '#', 'type' => 'submit', 'tooltip' => ''])
@include('laravelkit::kit.kern.partials.symbol-button', ['variante' => 'save', 'typ' => $type, 'route' => $route, 'tooltip' => $tooltip])
