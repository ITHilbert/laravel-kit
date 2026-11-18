{{-- Kern-Theme: verstecktes select; js/combobox.js ergänzt Eingabe, Auslöser und Liste --}}
@props(['label' => null, 'options' => [], 'value' => null, 'addClass' => '', 'error' => null])
@php
    $name = (string) $attributes->get('name');
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $mitLabel = (string) $label !== '';
    $gewaehlt = $value !== null ? (string) $value : \ITHilbert\LaravelKit\Kit\KitFeld::alt($name);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-combobox', $addClass, 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ($mitLabel)<label class="kit-combobox__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <select {{ ($mitLabel ? $z['attribute']->merge(['id' => $z['id']]) : $z['attribute'])->merge(['data-kit-combobox' => ''])->class(['kit-combobox__select']) }}>
        {{ $slot }}
        @foreach (\ITHilbert\LaravelKit\Kit\KitCombobox::auswahl($options) as $option)
            <option value="{{ $option['wert'] }}" @selected($gewaehlt !== null && $gewaehlt === $option['wert'])>{{ $option['text'] }}</option>
        @endforeach
    </select>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-combobox__error'])@endif

</div>
