{{-- Kern-Theme: Props wie bootstrap5/button-show --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Anzeigen'])
@include('laravelkit::kit.kern.partials.symbol-button', ['variante' => 'show', 'typ' => $type, 'route' => $route, 'tooltip' => $tooltip])
