{{-- Vorlage: vue/tailwind/forms/input-password.vue (ohne den Anzeigen-Schalter, der Vue braucht) --}}
@props(['name' => null, 'id' => null, 'label' => null])
<div class="w-full">
    @if ($label)
        <label for="{{ $id ?: $name }}" class="hlabel">{{ $label }}</label>
    @endif
    <div class="relative flex w-full">
        <input {{ $attributes->merge(['type' => 'password', 'id' => $id ?: $name, 'name' => $name, 'class' => 'input-password pr-12']) }}>
    </div>
</div>
