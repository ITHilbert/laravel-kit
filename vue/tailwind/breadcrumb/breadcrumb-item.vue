<template>
    <li v-if="href === '#'" :class="['breadcrumb-item active']" aria-current="page" v-bind="$attrs">
        <slot></slot>
    </li>
    <li v-else :class="['breadcrumb-item']" v-bind="$attrs">
        <a :href="href" class="breadcrumb-link">
            <slot></slot>
        </a>
    </li>
</template>

<script setup lang="ts">
defineProps<{
    href?: string;
}>();

defineOptions({ inheritAttrs: false });
</script>
