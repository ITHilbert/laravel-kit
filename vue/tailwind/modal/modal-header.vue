<template>
    <div class="modal-header" v-bind="$attrs">
        <h5 class="modal-title"><slot></slot></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
