<template >
   <div class="flex items-center gap-2 mb-2">
        <input type="checkbox" :value="value" class="checkbox" :name="name" :id="name" v-bind="$attrs" :checked="isChecked">
        <label class="hlabel !mb-0 cursor-pointer" :for="name"><slot></slot></label>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { istAngehakt } from '../../shared/checkboxChecked.mjs';

const props = defineProps<{ 
    name?: string;
    value?: any;
    checked?: any;
}>();

defineOptions({ inheritAttrs: false });

const isChecked = computed(() => istAngehakt(props.checked, props.value));
</script>
