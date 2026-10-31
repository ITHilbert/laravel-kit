{{-- Vorlage: vue/bootstrap5/forms/input-text.vue --}}
@props(['name' => ''])
<input {{ $attributes->merge(['type' => 'text', 'id' => $name, 'name' => $name, 'class' => 'form-control input-text']) }}>
