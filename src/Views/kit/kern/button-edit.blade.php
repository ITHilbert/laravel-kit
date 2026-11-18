{{-- Kern-Theme: Props wie bootstrap5/button-edit --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Bearbeiten'])
@include('laravelkit::kit.kern.partials.symbol-button', ['variante' => 'edit', 'typ' => $type, 'route' => $route, 'tooltip' => $tooltip])
