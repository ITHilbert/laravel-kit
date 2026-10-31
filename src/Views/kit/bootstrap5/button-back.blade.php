{{-- Vorlage: vue/bootstrap5/buttons/button-back.vue --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => ''])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-back', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-undo"></i> {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-back', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-undo"></i> {{ $slot }}</button>
@endif
