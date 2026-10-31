{{-- Vorlage: vue/tailwind/forms/text-area.vue — Inhalt: value, sonst modelValue, sonst Slot --}}
@props(['name' => null, 'rows' => null, 'modelValue' => null, 'value' => null])
<textarea {{ $attributes->merge(['id' => $name, 'name' => $name, 'class' => 'text-area', 'rows' => $rows]) }}>@if ($value !== null){{ $value }}@elseif (filled($modelValue)){{ $modelValue }}@else{{ $slot }}@endif</textarea>
