<template>
    <div :value="value" class="show-euro" v-bind="$attrs">
        {{ formatValue(value) }}
    </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    value?: string | number;
}>(), {
    value: ''
});

defineOptions({ inheritAttrs: false });

function formatValue(value: string | number) {
    if (value === '' || value === null || value === undefined) return '';
    let val = (Number(value)).toFixed(2).replace('.', ',');
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ' €';
}
</script>
