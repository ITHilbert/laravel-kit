{{-- Vorlage: vue/tailwind/forms/input-password.vue (ohne den Anzeigen-Schalter, der Vue braucht) --}}
@props(['name' => null, 'id' => null, 'label' => null, 'error' => null])
@php($z = \ITHilbert\LaravelKit\Kit\KitFeld::zustand((string) $name, $id, $attributes, $error))
<div class="w-full">
    @if ($label)
        <label for="{{ $z['id'] }}" class="hlabel">{{ $label }}</label>
    @endif
    <div class="relative flex w-full">
        <input {{ $z['attribute']->merge(['type' => 'password', 'id' => $z['id'], 'name' => $name, 'class' => 'input-password pr-12']) }}>
    </div>@if ($z['meldung'] !== null)@include('laravelkit::kit.tailwind.partials.fehler', ['id' => $z['fehlerId'], 'meldung' => $z['meldung']])@endif

</div>
