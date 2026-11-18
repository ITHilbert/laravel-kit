{{-- Kern-Theme: Props wie bootstrap5/button-create --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Neuen Eintrag erstellen'])
@include('laravelkit::kit.kern.partials.symbol-button', ['variante' => 'create', 'typ' => $type, 'route' => $route, 'tooltip' => $tooltip])
