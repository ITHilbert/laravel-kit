{{-- Vorlage: vue/bootstrap5/forms/input-file-img.vue --}}
@props(['name' => ''])
<input {{ $attributes->merge(['type' => 'file', 'id' => $name, 'name' => $name, 'class' => 'form-control', 'accept' => 'image/png, image/jpeg']) }} />
