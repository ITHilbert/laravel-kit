<template>
    <li class="nav-item">
        <a :class="['nav-link', addClass]" :href="href" v-bind="$attrs"> 
            <span class="nav-link-icon" :data-feather="img"></span>
            <span><slot></slot></span>
        </a>
    </li>
</template>

<script setup lang="ts">
defineProps<{
    href?: string;
    img?: string;
    addClass?: string;
}>();

defineOptions({ inheritAttrs: false });
</script>
