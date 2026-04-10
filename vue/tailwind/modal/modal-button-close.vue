<template>
    <button type="button" class="modal-button-close" data-bs-dismiss="modal" v-bind="$attrs"><slot></slot></button>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
