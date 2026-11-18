{{-- Kern-Theme: wie bootstrap5/breadcrumb --}}
<nav {{ $attributes->merge(['aria-label' => 'breadcrumb'])->class(['kit-breadcrumb']) }}>
    <ol class="kit-breadcrumb__list">
        {{ $slot }}
    </ol>
</nav>
