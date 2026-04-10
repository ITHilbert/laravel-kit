<template>
    <div :value="value" class="show-date" v-bind="$attrs">
        {{ formatValue(value) }}
    </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    value?: string;
}>(), {
    value: ''
});

defineOptions({ inheritAttrs: false });

function formatValue(value: string) {
    if (!value) return '';
    const tmp = value.split('-');
    if (tmp.length === 3) {
        return tmp[2] + "." + tmp[1] + "." + tmp[0];
    }
    return value;
}
</script>
