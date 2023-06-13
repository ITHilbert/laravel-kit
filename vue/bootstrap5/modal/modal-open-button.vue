<!-- Dieser Button öffnet den Modal Dialog -->
<template>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" :data-bs-target="modalId">
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
