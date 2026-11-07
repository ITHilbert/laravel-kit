{{-- Vorlage: vue/tailwind/forms/combobox.vue — options: cbKey/cbCaption (Array, Collection, JSON-Text) oder Schlüssel→Text; ausgewählt ist value, sonst old() --}}
@props(['name' => '', 'id' => null, 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, $id, $attributes, $error);
    $feldId = $z['id'];
    $gewaehlt = \ITHilbert\LaravelKit\Kit\KitFeld::gewaehlt($name, $value, $attributes->has('multiple'));
@endphp
<div class="w-full">
    @if ($label)
        <x-kit::label :for="$feldId">{{ $label }}</x-kit::label>
    @endif
    <select {{ $z['attribute']->merge(['id' => $feldId, 'name' => $name, 'class' => 'select combobox']) }}>
        @if ($placeholder !== null && $placeholder !== '')
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach (\ITHilbert\LaravelKit\Kit\KitCombobox::auswahl($options) as $option)
            <option value="{{ $option['wert'] }}" @selected(in_array($option['wert'], $gewaehlt, true))>{{ $option['text'] }}</option>
        @endforeach
    </select>@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

</div>
