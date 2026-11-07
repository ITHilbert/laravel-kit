{{-- Vorlage: vue/bootstrap5/forms/input-euro.vue --}}
@props(['label' => null, 'name' => '', 'step' => 0.01, 'invalid' => false, 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
@if ((string) $label !== '')<x-kit::label :for="$z['id']">{{ $label }}</x-kit::label>@endif<div class="input-group">
    <input {{ $z['attribute']->merge(['type' => 'number', 'id' => $z['id'], 'name' => $name, 'step' => $step, 'lang' => 'de-DE', 'aria-label' => 'Euro', 'style' => '-webkit-appearance: none; -moz-appearance: textfield;'] + ($alt !== null ? ['value' => $alt] : []))->class(['form-control input-euro', $addClass, 'is-invalid' => $z['meldung'] !== null]) }}>
    <span class="input-group-text"> &euro; </span>
</div>@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

