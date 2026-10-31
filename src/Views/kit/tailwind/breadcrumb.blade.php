{{-- Vorlage: vue/tailwind/breadcrumb/breadcrumb.vue --}}
<nav {{ $attributes->merge(['aria-label' => 'breadcrumb']) }}>
    <ol class="breadcrumb">
        {{ $slot }}
    </ol>
</nav>
