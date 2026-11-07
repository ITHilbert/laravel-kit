{{-- Vorlage: vue/tailwind/forms/input-file.vue — ein Wert wird nie vorbelegt (auch nicht aus old()) --}}
@props(['name' => '', 'id' => null, 'label' => null, 'value' => null])
@php($feldId = \ITHilbert\LaravelKit\Kit\KitFeld::id($name, $id))
<div class="w-full">
    @if ($label)
        <x-kit::label :for="$feldId">{{ $label }}</x-kit::label>
    @endif
    <input {{ $attributes->merge(['type' => 'file', 'id' => $feldId, 'name' => $name, 'class' => 'input-file px-3 py-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500']) }} />
</div>
