{{-- Vorlage: vue/tailwind/tabs/tab-header.vue — aktiver Reiter per active, Umschalten per js/tabs.js --}}
@props(['name' => null, 'active' => false])
<li class="block" role="presentation">
    <button {{ $attributes->merge(['type' => 'button', 'id' => $name.'-tab', 'role' => 'tab', 'aria-selected' => $active ? 'true' : 'false', 'data-kit-tab' => $name])->class(['tab-header', 'active' => $active]) }}>
        {{ $slot }}
    </button>
</li>
