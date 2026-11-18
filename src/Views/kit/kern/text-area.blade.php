{{-- Kern-Theme: Inhalt: Slot, sonst value, sonst old() --}}
@props(['label' => null, 'name' => '', 'rows' => '3', 'value' => null, 'addClass' => '', 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
    $wurzel = \Illuminate\Support\Arr::toCssClasses(['kit-textarea', $addClass, 'is-invalid' => $z['meldung'] !== null]);
@endphp
<div class="{{ $wurzel }}">
    @if ((string) $label !== '')<label class="kit-textarea__label" for="{{ $z['id'] }}">{{ $label }}</label>@endif

    <textarea {{ $z['attribute']->merge(['id' => $z['id'], 'name' => $name, 'rows' => $rows])->class(['kit-textarea__input']) }}>@if ($slot->isNotEmpty()){{ $slot }}@elseif ($value !== null){{ $value }}@else{{ $alt }}@endif</textarea>
    @if ($z['meldung'] !== null)@include('laravelkit::kit.kern.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung'], 'klasse' => 'kit-textarea__error'])@endif

</div>
