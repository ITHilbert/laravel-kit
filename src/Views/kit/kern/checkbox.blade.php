{{-- Kern-Theme: angehakt per checked, value ist der gesendete Wert --}}
@props(['name' => '', 'id' => null, 'addclass' => '', 'value' => 1, 'checked' => false])
<div class="{{ \Illuminate\Support\Arr::toCssClasses(['kit-checkbox', $addclass]) }}">
    <input {{ $attributes->merge(['type' => 'checkbox', 'value' => $value, 'name' => $name, 'id' => $id ?: $name])->class(['kit-checkbox__input']) }} @checked($checked)>
    <label class="kit-checkbox__label" for="{{ $id ?: $name }}">{{ $slot }}</label>
</div>
