{{-- Kern-Theme: Markup wie vue3/components/feld/text-feld.vue --}}
@props(['label' => null, 'name' => '', 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-input-text', $addClass, 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ((string) $label !== '')<label class="kit-input-text__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <input {{ $z['attribute']->merge(['type' => 'text', 'id' => $z['id'], 'name' => $name] + ($alt !== null ? ['value' => $alt] : []))->class(['kit-input-text__input']) }}>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-input-text__error'])@endif

</div>
