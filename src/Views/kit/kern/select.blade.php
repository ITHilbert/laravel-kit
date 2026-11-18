{{-- Kern-Theme: options: cbKey/cbCaption oder Schlüssel→Text; ausgewählt ist value, sonst old() --}}
@props(['name' => '', 'id' => null, 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, $id, $attributes, $error);
    $gewaehlt = \ITHilbert\LaravelKit\Kit\KitFeld::gewaehlt($name, $value, $attributes->has('multiple'));
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-select', 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ($label)<label class="kit-select__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <select {{ $z['attribute']->merge(['id' => $z['id'], 'name' => $name])->class(['kit-select__input']) }}>
        @if ($placeholder !== null && $placeholder !== '')
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach (\ITHilbert\LaravelKit\Kit\KitCombobox::auswahl($options) as $option)
            <option value="{{ $option['wert'] }}" @selected(in_array($option['wert'], $gewaehlt, true))>{{ $option['text'] }}</option>
        @endforeach
    </select>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-select__error'])@endif

</div>
