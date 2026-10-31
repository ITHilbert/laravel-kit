{{-- Vorlage: vue/bootstrap5/forms/text-area.vue — Inhalt: Slot, sonst value --}}
@props(['name' => '', 'rows' => '3', 'value' => '', 'addClass' => ''])
<textarea {{ $attributes->merge(['id' => $name, 'name' => $name, 'rows' => $rows])->class(['form-control text-area', $addClass]) }}>@if ($slot->isNotEmpty()){{ $slot }}@else{{ $value }}@endif</textarea>
