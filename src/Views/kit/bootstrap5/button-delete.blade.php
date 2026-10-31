{{-- Vorlage: vue/bootstrap5/buttons/button-delete.vue --}}
{{-- value: ID des Datensatzes, wird als data-kit-delete für den Lösch-Dialog (js/loeschen.js) ausgegeben. --}}
@props(['route' => '#', 'type' => 'button', 'tooltip' => 'Löschen', 'value' => null])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-delete', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-minus-circle"></i> {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-delete', 'data-toggle' => 'tooltip', 'title' => $tooltip, 'data-kit-delete' => $value]) }}><i class="fas fa-minus-circle"></i> {{ $slot }}</button>
@endif
