{{-- Vorlage: vue/tailwind/forms/hlabel.vue — for und weitere Attribute landen am label --}}
<label {{ $attributes->merge(['class' => 'hlabel']) }}>{{ $slot }}</label>
