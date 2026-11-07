{{-- Vorlage: vue/bootstrap5/forms/text-area.vue — Inhalt: Slot, sonst value, sonst old() --}}
@props(['label' => null, 'name' => '', 'rows' => '3', 'value' => null, 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
@if ((string) $label !== '')<x-kit::label :for="$z['id']">{{ $label }}</x-kit::label>@endif<textarea {{ $z['attribute']->merge(['id' => $z['id'], 'name' => $name, 'rows' => $rows])->class(['form-control text-area', $addClass, 'is-invalid' => $z['meldung'] !== null]) }}>@if ($slot->isNotEmpty()){{ $slot }}@elseif ($value !== null){{ $value }}@else{{ $alt }}@endif</textarea>@if ($z['meldung'] !== null)@include('laravelkit::kit.bootstrap5.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

