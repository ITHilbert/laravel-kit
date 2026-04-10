<template>
    <div class="card-main-header" v-bind="$attrs">
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
