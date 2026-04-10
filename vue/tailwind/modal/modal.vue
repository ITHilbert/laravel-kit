<template>
    <div class="modal" tabindex="-1" :id="id" v-bind="$attrs">
        <div class="modal-dialog">
            <div class="modal-content">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    id: string;
}>();

defineOptions({ inheritAttrs: false });
</script>
