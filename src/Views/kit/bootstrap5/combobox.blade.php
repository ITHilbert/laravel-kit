{{-- Vorlage: vue/bootstrap5/forms/combobox.vue — options: Liste mit cbKey/cbCaption (Array, Collection oder JSON-Text); ohne value gilt old() --}}
@props(['label' => null, 'options' => [], 'value' => null, 'addClass' => '', 'error' => null])
@php
    $name = (string) $attributes->get('name');
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $mitLabel = (string) $label !== '';
    $gewaehlt = $value !== null ? (string) $value : \ITHilbert\LaravelKit\Kit\KitFeld::alt($name);
@endphp
@if ($mitLabel)<x-kit::label :for="$z['id']">{{ $label }}</x-kit::label>@endif<select {{ ($mitLabel ? $z['attribute']->merge(['id' => $z['id']]) : $z['attribute'])->class(['form-control combobox', $addClass, 'is-invalid' => $z['meldung'] !== null]) }}>
    {{ $slot }}
    @foreach (\ITHilbert\LaravelKit\Kit\KitCombobox::optionen($options) as $option)
        <option value="{{ $option['wert'] }}" @selected($gewaehlt !== null && $gewaehlt === $option['wert'])>{{ $option['text'] }}</option>
    @endforeach
</select>@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

