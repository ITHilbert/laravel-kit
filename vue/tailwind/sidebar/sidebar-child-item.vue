<template>
    <li class="sidebar-item-wrapper block">
        <a :class="['sidebar-link group', { 'active': isActive }, addClass]" :href="href" v-bind="$attrs">
            <span class="sidebar-link-text">
                <slot></slot>
            </span>
        </a>
    </li>
</template>

<script setup lang="ts">
import { ref, onMounted, inject } from 'vue';

const props = withDefaults(defineProps<{
    href?: string;
    img?: string;
    addClass?: string;
    active?: boolean;
    activeOnSub?: boolean;
}>(), {
    href: '#',
    img: 'home',
    addClass: '',
    active: false,
    activeOnSub: true
});

defineOptions({ inheritAttrs: false });

const isActive = ref(props.active);
const setParentActive = inject<() => void>('setParentActive', () => {});

onMounted(() => {
    if (typeof window !== 'undefined') {
        const relativePath = props.href.replace(window.location.origin, '');
        const currentPath = window.location.pathname.replace(/\/$/, '');
        
        if (props.activeOnSub) {
            if (currentPath.startsWith(relativePath)) {
                isActive.value = true;
                setParentActive();
            } else {
                isActive.value = false;
            }
        } else {
            if (currentPath === relativePath) {
                isActive.value = true;
                setParentActive();
            } else {
                isActive.value = false;
            }
        }
    }
});
</script>
