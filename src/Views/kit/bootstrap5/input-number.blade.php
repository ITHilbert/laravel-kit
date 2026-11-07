{{-- Vorlage: vue/bootstrap5/forms/input-number.vue --}}
@props(['label' => null, 'name' => '', 'step' => 0.01, 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
@if ((string) $label !== '')<x-kit::label :for="$z['id']">{{ $label }}</x-kit::label>@endif<input {{ $z['attribute']->merge(['type' => 'number', 'id' => $z['id'], 'name' => $name, 'step' => $step ] + ($alt !== null ? ['value' => $alt] : []))->class(['form-control input-number', $addClass, 'is-invalid' => $z['meldung'] !== null]) }}>@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

