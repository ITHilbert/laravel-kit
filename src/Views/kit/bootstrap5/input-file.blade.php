{{-- Vorlage: vue/bootstrap5/forms/input-file-img.vue — ein Wert wird nie vorbelegt (auch nicht aus old()) --}}
@props(['name' => '', 'id' => null, 'label' => null, 'value' => null])
@php($feldId = \ITHilbert\LaravelKit\Kit\KitFeld::id($name, $id))
<div>
    @if ($label)
        <x-kit::label :for="$feldId">{{ $label }}</x-kit::label>
    @endif
    <input {{ $attributes->merge(['type' => 'file', 'id' => $feldId, 'name' => $name, 'class' => 'form-control']) }} />
</div>
