{{-- Vorlage: vue/tailwind/buttons/button-delete.vue --}}
{{-- value: ID des Datensatzes, wird als data-kit-delete für den Lösch-Dialog (js/loeschen.js) ausgegeben. --}}
@props(['route' => '#', 'type' => 'button', 'tooltip' => 'Löschen', 'value' => null])
@if ($type === 'link')
<a {{ $attributes->merge(['href' => $route, 'class' => 'btn btn-delete', 'title' => $tooltip]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
        <path d="M10 11v6"/>
        <path d="M14 11v6"/>
        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
    </svg>
    {{ $slot }}
</a>
@else
<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-delete', 'title' => $tooltip, 'data-kit-delete' => $value]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
        <path d="M10 11v6"/>
        <path d="M14 11v6"/>
        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
    </svg>
    {{ $slot }}
</button>
@endif
