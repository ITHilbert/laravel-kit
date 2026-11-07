{{-- Vorlage: vue/bootstrap5/forms/input-file-img.vue — ein Wert wird nie vorbelegt (auch nicht aus old()) --}}
@props(['name' => '', 'id' => null, 'label' => null, 'value' => null, 'error' => null])
@php($z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, $id, $attributes, $error))
<div>
    @if ($label)
        <x-kit::label :for="$z['id']">{{ $label }}</x-kit::label>
    @endif
    <input {{ $z['attribute']->merge(['type' => 'file', 'id' => $z['id'], 'name' => $name, 'class' => 'form-control'.($z['meldung'] !== null ? ' is-invalid' : '')]) }} />@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

</div>
