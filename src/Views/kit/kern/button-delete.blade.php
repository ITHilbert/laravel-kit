{{-- Kern-Theme: Props wie bootstrap5/button-delete; value wird als data-kit-delete ausgegeben (js/loeschen.js) --}}
@props(['route' => '#', 'type' => 'button', 'tooltip' => 'Löschen', 'value' => null])
@include('laravelkit::kit.kern.partials.symbol-button', ['variante' => 'delete', 'typ' => $type, 'route' => $route, 'tooltip' => $tooltip, 'loeschId' => $value])
