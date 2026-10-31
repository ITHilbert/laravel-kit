{{-- Vorlage: vue/bootstrap5/forms/input-date.vue --}}
@props(['name' => '', 'addClass' => ''])
<input {{ $attributes->merge(['type' => 'date', 'id' => $name, 'name' => $name])->class(['form-control input-date', $addClass]) }}>
