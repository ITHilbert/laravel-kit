{{-- Vorlage: vue/bootstrap5/buttons/button-edit.vue --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Bearbeiten'])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-edit', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-pencil-alt"></i> {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-edit', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-pencil-alt"></i> {{ $slot }}</button>
@endif
