{{-- Vorlage: vue/bootstrap5/forms/input-text.vue --}}
@props(['name' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
<input {{ $z['attribute']->merge(['type' => 'text', 'id' => $z['id'], 'name' => $name, 'class' => 'form-control input-text'.($z['meldung'] !== null ? ' is-invalid' : '')] + ($alt !== null ? ['value' => $alt] : [])) }}>@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

