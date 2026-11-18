{{-- Kern-Theme: Props wie bootstrap5/button-cancel --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Abbruch'])
@include('laravelkit::kit.kern.partials.symbol-button', ['variante' => 'cancel', 'typ' => $type, 'route' => $route, 'tooltip' => $tooltip])
