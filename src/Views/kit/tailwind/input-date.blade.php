{{-- Vorlage: vue/tailwind/forms/input-date.vue --}}
@props(['name' => null])
<input {{ $attributes->merge(['type' => 'date', 'id' => $name, 'name' => $name, 'class' => 'input-date']) }}>
