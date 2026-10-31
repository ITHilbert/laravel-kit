{{-- Vorlage: vue/bootstrap5/forms/checkbox.vue — angehakt per checked, value ist der gesendete Wert --}}
@props(['name' => '', 'id' => null, 'addclass' => '', 'value' => 1, 'checked' => false])
<div class="form-check">
    <input {{ $attributes->merge(['type' => 'checkbox', 'value' => $value, 'name' => $name, 'id' => $id ?: $name])->class(['form-check-input', $addclass]) }} @checked($checked)>
    <label class="form-check-label" for="{{ $id ?: $name }}">{{ $slot }}</label>
</div>
