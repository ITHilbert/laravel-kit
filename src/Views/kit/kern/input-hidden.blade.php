{{-- Kern-Theme: nur das Feld, keine Klasse --}}
@props(['addClass' => ''])
<input {{ $attributes->merge(['type' => 'hidden'])->when(filled($addClass), fn ($bag) => $bag->class($addClass)) }}>
