<template>
    <nav class="sidebar" v-bind="$attrs">
        <div class="sidebar-content">
            <ul class="sidebar-nav">
                <slot></slot>
            </ul>
        </div>
        <div class="sidebar-footer">
            <button type="button" class="sidebar-footer-btn">
                <span class="sidebar-link-icon mr-2">
                    <span data-feather="refresh-cw"></span>
                </span>
                <span>Collapsed View</span>
            </button>
        </div>
    </nav>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
