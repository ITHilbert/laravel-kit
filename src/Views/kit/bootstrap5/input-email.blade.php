{{-- Vorlage: vue/bootstrap5/forms/input-email.vue --}}
@props(['name' => '', 'addClass' => ''])
<input {{ $attributes->merge(['type' => 'email', 'id' => $name, 'name' => $name])->class(['form-control input-email', $addClass]) }}>
