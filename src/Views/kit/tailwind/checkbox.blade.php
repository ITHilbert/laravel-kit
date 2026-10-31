{{-- Vorlage: vue/tailwind/forms/checkbox.vue — `checked` entscheidet, ist es null, gilt: angehakt bei value 1 bzw. true --}}
@props(['name' => null, 'value' => null, 'checked' => null])
<div class="flex items-center gap-2 mb-2">
    <input {{ $attributes->merge(['type' => 'checkbox', 'value' => $value, 'class' => 'checkbox', 'name' => $name, 'id' => $name]) }} @checked($checked ?? ($value == 1 || $value === true))>
    <label class="hlabel !mb-0 cursor-pointer" for="{{ $name }}">{{ $slot }}</label>
</div>
