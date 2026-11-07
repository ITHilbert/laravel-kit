{{-- Vorlage: vue/tailwind/forms/input-file.vue — ein Wert wird nie vorbelegt (auch nicht aus old()) --}}
@props(['name' => '', 'id' => null, 'label' => null, 'value' => null, 'error' => null])
@php($z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, $id, $attributes, $error))
<div class="w-full">
    @if ($label)
        <x-kit::label :for="$z['id']">{{ $label }}</x-kit::label>
    @endif
    <input {{ $z['attribute']->merge(['type' => 'file', 'id' => $z['id'], 'name' => $name, 'class' => 'input-file px-3 py-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500']) }} />@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

</div>
