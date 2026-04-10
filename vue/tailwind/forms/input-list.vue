<template>
    <div>
        <input :list="name" :value="value" :name="name" class="input-list" v-bind="$attrs">
        <datalist :id="name">
            <slot></slot>
            <template v-if="hasOptions">
                <template v-for="option in options" :key="option.cbKey">
                    <option :value="option.cbKey" :selected="option.cbKey === value">
                        {{ option.cbCaption }}
                    </option>
                </template>
            </template>
        </datalist>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    options?: any;
    value?: any;
    name?: string;
}>();

defineOptions({ inheritAttrs: false });

const hasOptions = computed(() => {
    return typeof props.options === 'object' && props.options !== null && props.options.length !== undefined && props.options.length > 0;
});
</script>
