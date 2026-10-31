{{-- Vorlage: vue/tailwind/forms/input-email.vue --}}
@props(['name' => null])
<input {{ $attributes->merge(['type' => 'email', 'id' => $name, 'name' => $name, 'class' => 'input-email']) }}>
