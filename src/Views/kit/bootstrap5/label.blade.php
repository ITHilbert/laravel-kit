{{-- Bootstrap-5-Label — for und weitere Attribute landen am label --}}
<label {{ $attributes->merge(['class' => 'form-label']) }}>{{ $slot }}</label>
