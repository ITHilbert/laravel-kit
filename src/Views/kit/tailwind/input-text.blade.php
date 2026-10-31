{{-- Vorlage: vue/tailwind/forms/input-text.vue --}}
@props(['name' => null, 'id' => null, 'label' => null])
<div class="w-full">
    @if ($label)
        <label for="{{ $id ?: $name }}" class="hlabel">{{ $label }}</label>
    @endif
    <input {{ $attributes->merge(['type' => 'text', 'id' => $id ?: $name, 'name' => $name, 'class' => 'input-text']) }}>
</div>
