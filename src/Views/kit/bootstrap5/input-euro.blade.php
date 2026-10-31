{{-- Vorlage: vue/bootstrap5/forms/input-euro.vue --}}
@props(['name' => '', 'step' => 0.01, 'invalid' => false, 'addClass' => ''])
<div class="input-group">
    <input {{ $attributes->merge(['type' => 'number', 'id' => $name, 'name' => $name, 'step' => $step, 'lang' => 'de-DE', 'aria-label' => 'Euro', 'style' => '-webkit-appearance: none; -moz-appearance: textfield;'])->class(['form-control input-euro', $addClass]) }}>
    <span class="input-group-text"> &euro; </span>
</div>
