{{-- Vorlage: vue/bootstrap5/forms/input-password.vue --}}
@props(['name' => '', 'addClass' => ''])
<div class="input-group">
    <input {{ $attributes->merge(['type' => 'password', 'id' => $name, 'name' => $name])->class(['form-control input-password', $addClass]) }}>
    <span class="input-group-text"><span data-feather="key"></span></span>
</div>
