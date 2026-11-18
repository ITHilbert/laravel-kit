{{-- Kern-Theme: Markup wie vue3/components/feld/text-feld.vue (ohne Schalter „anzeigen“) --}}
@props(['label' => null, 'name' => '', 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-input-password', $addClass, 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ((string) $label !== '')<label class="kit-input-password__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <input {{ $z['attribute']->merge(['type' => 'password', 'id' => $z['id'], 'name' => $name])->class(['kit-input-password__input']) }}>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-input-password__error'])@endif

</div>
