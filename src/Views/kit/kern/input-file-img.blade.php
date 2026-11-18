{{-- Kern-Theme: Dateifeld für Bilder --}}
@props(['name' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, null, $attributes, $error);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-input-file', 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    <input {{ $z['attribute']->merge(['type' => 'file', 'id' => $z['id'], 'name' => $name, 'accept' => 'image/png, image/jpeg'])->class(['kit-input-file__input']) }}>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-input-file__error'])@endif

</div>
