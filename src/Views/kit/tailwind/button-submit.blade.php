{{-- Vorlage: vue/tailwind/buttons/button-submit.vue --}}
@props(['tooltip' => ''])
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-submit', 'title' => $tooltip]) }}>
    {{ $slot }}
</button>
