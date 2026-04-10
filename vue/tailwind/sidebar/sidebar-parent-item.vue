<template>
    <div class="sidebar-item-wrapper">
        <a 
            :class="['sidebar-link group', { 'active': isActive }, addClass]" 
            :href="href" 
            role="button" 
            data-bs-toggle="collapse" 
            :aria-expanded="isActive" 
            :aria-controls="name" 
            @click="toggle"
            v-bind="$attrs"
        >
            <span class="sidebar-link-icon" v-if="img">
                <span :data-feather="img"></span>
            </span>
            <span class="sidebar-link-text">{{ title }}</span>
            <span class="sidebar-dropdown-indicator fas fa-caret-right"></span>
        </a>
        <div class="sidebar-child-wrapper">
            <ul :class="['sidebar-child-list', { 'collapse': true, 'show': isActive }]" :id="name">
                <slot></slot>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, provide } from 'vue';

const props = withDefaults(defineProps<{
    title?: string;
    img?: string;
    addClass?: string;
    active?: boolean;
}>(), {
    title: 'home',
    img: 'home',
    addClass: '',
    active: false,
});

defineOptions({ inheritAttrs: false });

const isActive = ref(props.active);

const name = computed(() => {
    return props.title ? props.title.replace(/ /g, '-') : ''; 
});

const href = computed(() => {
    return name.value ? `#${name.value}` : '';
});

function toggle() {
    isActive.value = !isActive.value;
}

function setParentActive() {
    isActive.value = true;
}

provide('setParentActive', setParentActive);
</script>
