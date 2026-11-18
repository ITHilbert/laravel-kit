{{-- Kern-Theme: ein Wert wird nie vorbelegt (auch nicht aus old()) --}}
@props(['name' => '', 'id' => null, 'label' => null, 'value' => null, 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, $id, $attributes, $error);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-input-file', 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ($label)<label class="kit-input-file__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <input {{ $z['attribute']->merge(['type' => 'file', 'id' => $z['id'], 'name' => $name])->class(['kit-input-file__input']) }}>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-input-file__error'])@endif

</div>
