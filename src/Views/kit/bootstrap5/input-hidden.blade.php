{{-- Vorlage: vue/bootstrap5/forms/input-hidden.vue --}}
@props(['addClass' => ''])
<input {{ $attributes->merge(['type' => 'hidden'])->when(filled($addClass), fn ($bag) => $bag->class($addClass)) }}>
