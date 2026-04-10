<template>
    <div class="card">
        <div class="card-header"><slot name="header">{{ title }}</slot></div>
        <div class="card-body">
            <slot></slot>
        </div>
        <div v-if="footervisible" class="card-footer"><slot name="footer"></slot></div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    title?: string;
    footervisible?: boolean;
}>();
</script>
