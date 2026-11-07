{{-- Vorlage: vue/tailwind/forms/input-text.vue --}}
@props(['name' => null, 'id' => null, 'label' => null, 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, $id, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
<div class="w-full">
    @if ($label)
        <label for="{{ $z['id'] }}" class="hlabel">{{ $label }}</label>
    @endif
    <input {{ $z['attribute']->merge(['type' => 'text', 'id' => $z['id'], 'name' => $name, 'class' => 'input-text'] + ($alt !== null ? ['value' => $alt] : [])) }}>@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

</div>
