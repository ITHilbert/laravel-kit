{{-- Vorlage: vue/bootstrap5/forms/input-file-img.vue --}}
@props(['name' => '', 'error' => null])
@php($z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, null, $attributes, $error))
<input {{ $z['attribute']->merge(['type' => 'file', 'id' => $z['id'], 'name' => $name, 'class' => 'form-control'.($z['meldung'] !== null ? ' is-invalid' : ''), 'accept' => 'image/png, image/jpeg']) }} />@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

