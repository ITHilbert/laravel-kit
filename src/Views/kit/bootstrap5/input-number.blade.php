{{-- Vorlage: vue/bootstrap5/forms/input-number.vue --}}
@props(['name' => '', 'step' => 0.01, 'addClass' => ''])
<input {{ $attributes->merge(['type' => 'number', 'id' => $name, 'name' => $name, 'step' => $step])->class(['form-control input-number', $addClass]) }}>
