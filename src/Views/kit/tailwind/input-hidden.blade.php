{{-- Vorlage: vue/tailwind/forms/input-hidden.vue --}}
@props(['addClass' => ''])
<input {{ $attributes->merge(['type' => 'hidden'])->when(filled($addClass), fn ($bag) => $bag->class($addClass)) }}>
