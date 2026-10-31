{{-- Vorlage: vue/bootstrap5/buttons/button-show.vue --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Anzeigen'])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-show', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-file-contract"></i> {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-show', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-file-contract"></i> {{ $slot }}</button>
@endif
