{{-- Kern-Theme: Markup wie vue3/components/feld/text-feld.vue --}}
@props(['label' => null, 'name' => '', 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-input-email', $addClass, 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ((string) $label !== '')<label class="kit-input-email__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <input {{ $z['attribute']->merge(['type' => 'email', 'id' => $z['id'], 'name' => $name] + ($alt !== null ? ['value' => $alt] : []))->class(['kit-input-email__input']) }}>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-input-email__error'])@endif

</div>
