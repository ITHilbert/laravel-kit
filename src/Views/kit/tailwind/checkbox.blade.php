{{-- Vorlage: vue/tailwind/forms/checkbox.vue — angehakt, wenn value 1 bzw. true ist --}}
@props(['name' => null, 'value' => null])
<div class="flex items-center gap-2 mb-2">
    <input {{ $attributes->except('checked')->merge(['type' => 'checkbox', 'value' => $value, 'class' => 'checkbox', 'name' => $name, 'id' => $name]) }} @checked($value == 1 || $value === true)>
    <label class="hlabel !mb-0 cursor-pointer" for="{{ $name }}">{{ $slot }}</label>
</div>
