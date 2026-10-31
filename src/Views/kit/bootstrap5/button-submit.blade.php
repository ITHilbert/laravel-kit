{{-- Vorlage: vue/bootstrap5/buttons/button-submit.vue --}}
@props(['tooltip' => ''])
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-submit', 'data-toggle' => 'tooltip', 'title' => $tooltip]) }}>{{ $slot }}</button>
