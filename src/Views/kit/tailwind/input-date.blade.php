{{-- Vorlage: vue/tailwind/forms/input-date.vue --}}
@props(['name' => null, 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
<input {{ $z['attribute']->merge(['type' => 'date', 'id' => $z['id'], 'name' => $name, 'class' => 'input-date'] + ($alt !== null ? ['value' => $alt] : [])) }}>@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

