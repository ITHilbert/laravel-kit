{{-- Vorlage: vue/bootstrap5/forms/input-password.vue --}}
@props(['label' => null, 'name' => '', 'addClass' => '', 'error' => null])
@php($z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, null, $attributes, $error))
@if ((string) $label !== '')<x-kit::label :for="$z['id']">{{ $label }}</x-kit::label>@endif<div class="input-group">
    <input {{ $z['attribute']->merge(['type' => 'password', 'id' => $z['id'], 'name' => $name])->class(['form-control input-password', $addClass, 'is-invalid' => $z['meldung'] !== null]) }}>
    <span class="input-group-text"><span data-feather="key"></span></span>
</div>@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

