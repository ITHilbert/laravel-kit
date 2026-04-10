<template >
   <div class="flex items-center gap-2 mb-2">
        <input type="radio" :id="id" class="radiobox" v-bind="$attrs" :checked="active">
        <label class="hlabel !mb-0 cursor-pointer" :for="id"><slot></slot></label>
    </div>
</template>

<script setup lang="ts">
defineProps<{ 
    id?: string;
    active?: boolean;
}>();
defineOptions({ inheritAttrs: false });
</script>
