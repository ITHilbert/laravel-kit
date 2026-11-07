{{-- Vorlage: vue/bootstrap5/forms/input-text.vue --}}
@props(['name' => ''])
@php($alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name))
<input {{ $attributes->merge(['type' => 'text', 'id' => $name, 'name' => $name, 'class' => 'form-control input-text'] + ($alt !== null ? ['value' => $alt] : [])) }}>
