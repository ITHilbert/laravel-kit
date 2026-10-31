{{-- Vorlage: vue/bootstrap5/buttons/button-cancel.vue --}}
@props(['route' => '#', 'type' => 'link', 'tooltip' => 'Abbruch'])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-cancel', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="far fa-times-circle"></i> {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-cancel', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="far fa-times-circle"></i> {{ $slot }}</button>
@endif
