<template>
    <select :value="value" class="combobox" v-bind="$attrs">
        <slot></slot>
        <template v-if="hasOptions">
            <template v-for="option in options" :key="option.cbKey">
                <option :value="option.cbKey" :selected="option.cbKey === value">
                    {{ option.cbCaption }}
                </option>
            </template>
        </template>
    </select>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    options?: any;
    value?: any;
}>();

defineOptions({ inheritAttrs: false });

const hasOptions = computed(() => {
    return typeof props.options === 'object' && props.options !== null && props.options.length !== undefined && props.options.length > 0;
});
</script>
