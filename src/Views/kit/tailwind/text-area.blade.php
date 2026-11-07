{{-- Vorlage: vue/tailwind/forms/text-area.vue — Inhalt: value, sonst modelValue, sonst Slot, sonst old() --}}
@props(['name' => null, 'rows' => null, 'modelValue' => null, 'value' => null, 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
<textarea {{ $z['attribute']->merge(['id' => $z['id'], 'name' => $name, 'class' => 'text-area', 'rows' => $rows]) }}>@if ($value !== null){{ $value }}@elseif (filled($modelValue)){{ $modelValue }}@elseif ($slot->isNotEmpty()){{ $slot }}@else{{ $alt }}@endif</textarea>@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

