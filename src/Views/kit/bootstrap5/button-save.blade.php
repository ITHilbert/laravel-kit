{{-- Vorlage: vue/bootstrap5/buttons/button-save.vue --}}
@props(['route' => '#', 'type' => 'submit', 'tooltip' => ''])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-save', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-save"></i> {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-save', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}><i class="fas fa-save"></i> {{ $slot }}</button>
@endif
