<template>
    <div class="card-deck" v-bind="$attrs">
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
