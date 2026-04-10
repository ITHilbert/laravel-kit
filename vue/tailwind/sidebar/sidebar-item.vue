<template>
    <div class="sidebar-item-wrapper">
        <a :class="['sidebar-link group', { 'active': isActive }, addClass]" :href="href" v-bind="$attrs">
            <span class="sidebar-link-icon" v-if="img">
                <span :data-feather="img"></span>
            </span>
            <span class="sidebar-link-text">
                <slot></slot>
            </span>
        </a>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

const props = withDefaults(defineProps<{
    href?: string;
    img?: string;
    addClass?: string;
    active?: boolean;
}>(), {
    href: '#',
    img: 'home',
    addClass: '',
    active: false,
});

defineOptions({ inheritAttrs: false });

const isActive = ref(props.active);

onMounted(() => {
    if (typeof window !== 'undefined') {
        const relativePath = props.href.replace(window.location.origin, '');
        const currentPath = window.location.pathname.replace(/\/$/, '');
        if (currentPath === relativePath) {
            isActive.value = true;
        } else {
            isActive.value = false;
        }
    }
});
</script>
