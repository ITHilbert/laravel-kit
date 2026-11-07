{{-- Vorlage: vue/bootstrap5/forms/input-email.vue --}}
@props(['name' => '', 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
<input {{ $z['attribute']->merge(['type' => 'email', 'id' => $z['id'], 'name' => $name ] + ($alt !== null ? ['value' => $alt] : []))->class(['form-control input-email', $addClass, 'is-invalid' => $z['meldung'] !== null]) }}>@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

