{{-- Vorlage: vue/tailwind/forms/combobox.vue — options: Liste mit cbKey/cbCaption (Array, Collection oder JSON-Text) --}}
@props(['options' => [], 'value' => null])
<select {{ $attributes->class(['combobox']) }}>
    {{ $slot }}
    @foreach (\ITHilbert\LaravelKit\Kit\KitCombobox::optionen($options) as $option)
        <option value="{{ $option['wert'] }}" @selected($value !== null && (string) $value === $option['wert'])>{{ $option['text'] }}</option>
    @endforeach
</select>
