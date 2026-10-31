{{-- Vorlage: vue/bootstrap5/buttons/button-create.vue --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Neuen Eintrag erstellen'])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-create', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-plus-circle"></i> {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-create', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-plus-circle"></i> {{ $slot }}</button>
@endif
