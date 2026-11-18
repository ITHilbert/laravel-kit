{{-- Kern-Theme: Markup wie vue3/components/feld/text-feld.vue --}}
@props(['label' => null, 'name' => '', 'step' => 0.01, 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-input-number', $addClass, 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ((string) $label !== '')<label class="kit-input-number__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <input {{ $z['attribute']->merge(['type' => 'number', 'id' => $z['id'], 'name' => $name, 'step' => $step] + ($alt !== null ? ['value' => $alt] : []))->class(['kit-input-number__input']) }}>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-input-number__error'])@endif

</div>
