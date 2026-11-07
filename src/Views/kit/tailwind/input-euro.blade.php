{{-- Vorlage: vue/tailwind/forms/input-euro.vue --}}
@props(['name' => null, 'step' => null, 'error' => null])
@php
    $z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, null, $attributes, $error);
    $alt = $attributes->has('value') ? null : \ITHilbert\LaravelKit\Kit\KitFeld::alt((string) $name);
@endphp
<div class="flex w-full rounded-md shadow-sm">
    <input {{ $z['attribute']->merge(['type' => 'number', 'id' => $z['id'], 'name' => $name, 'class' => 'input-euro flex-1 rounded-r-none border-r-0', 'step' => $step, 'lang' => 'de-DE', 'aria-label' => 'Euro', 'style' => '-webkit-appearance: none; -moz-appearance: textfield;'] + ($alt !== null ? ['value' => $alt] : [])) }}>
    <span class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">
        &euro;
    </span>
</div>@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

