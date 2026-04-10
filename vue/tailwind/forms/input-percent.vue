<template>
    <div class="flex w-full rounded-md shadow-sm">
        <input
            type="number"
            :id="name"
            :name="name"
            class="input-percent flex-1 rounded-r-none border-r-0"
            :step="step"
            aria-label="Prozent"
            v-bind="$attrs"
        >
        <span class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">
            %
        </span>
    </div>
</template>

<script setup lang="ts">
defineProps<{ name?: string; step?: number }>();
defineOptions({ inheritAttrs: false });
</script>
