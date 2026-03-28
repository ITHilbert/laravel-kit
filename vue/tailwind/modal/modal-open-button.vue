<!-- Dieser Button öffnet den Modal Dialog -->
<template>
    <button type="button" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500" data-bs-toggle="modal" :data-bs-target="modalId">
        <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        modalId: {
            type: String,
            required: true
        }
    }
}
</script>
