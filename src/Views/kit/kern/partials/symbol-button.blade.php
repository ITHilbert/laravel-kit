{{-- Kern-Theme: gemeinsamer Aufbau der Symbol-Buttons. Erwartet $variante, $typ, $route, $tooltip, optional $loeschId; $attributes und $slot kommen vom Baustein. --}}
@php
    $icon = match ($variante) {
        'back' => '<path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-15-6.7L3 13"/>',
        'cancel' => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/>',
        'create' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>',
        'delete' => '<circle cx="12" cy="12" r="10"/><path d="M8 12h8"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8"/>',
        'show' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>',
        default => '',
    };
    $symbol = $icon === '' ? '' : '<svg class="kit-button__icon" aria-hidden="true" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'.$icon.'</svg>';
@endphp
@if ($typ === 'link')
<a {{ $attributes->merge(['href' => $route, 'title' => $tooltip, 'data-variant' => $variante])->class(['kit-button']) }}>{!! $symbol !!} {{ $slot }}</a>
@else
<button {{ $attributes->merge(['type' => $typ, 'title' => $tooltip, 'data-variant' => $variante, 'data-kit-delete' => $loeschId ?? null])->class(['kit-button']) }}>{!! $symbol !!} {{ $slot }}</button>
@endif
