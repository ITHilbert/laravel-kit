<template >
   <div class="flex items-center gap-2 mb-2">
        <input type="checkbox" :value="value" class="checkbox" :name="name" :id="name" v-bind="$attrs" :checked="isChecked">
        <label class="hlabel !mb-0 cursor-pointer" :for="name"><slot></slot></label>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ 
    name?: string;
    value?: any;
}>();

defineOptions({ inheritAttrs: false });

const isChecked = computed(() => {
    return props.value == 1 || props.value === true;
});
</script>
