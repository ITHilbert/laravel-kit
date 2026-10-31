{{-- Vorlage: vue/tailwind/forms/input-number.vue --}}
@props(['name' => null, 'step' => null])
<input {{ $attributes->merge(['type' => 'number', 'id' => $name, 'name' => $name, 'class' => 'input-number', 'step' => $step]) }}>
