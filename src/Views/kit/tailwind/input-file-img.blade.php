{{-- Vorlage: vue/tailwind/forms/input-file-img.vue --}}
@props(['name' => '', 'error' => null])
@php($z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand($name, null, $attributes, $error))
<input {{ $z['attribute']->merge(['type' => 'file', 'id' => $z['id'], 'name' => $name, 'class' => 'input-file-img px-3 py-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500', 'accept' => 'image/png, image/jpeg']) }} />@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

