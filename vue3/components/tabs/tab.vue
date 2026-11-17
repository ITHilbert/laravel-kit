<script setup lang="ts">
// Ein Tab; muss in <kit-tabs> stehen. Zustände: is-active, disabled.
import { computed } from 'vue'
import { TabsTrigger, injectTabsRootContext } from 'reka-ui'

defineOptions({ name: 'KitTab' })

const props = defineProps<{ value: string; disabled?: boolean }>()

const wurzel = injectTabsRootContext()
const aktiv = computed(() => wurzel.modelValue.value === props.value)
</script>

<template>
  <TabsTrigger
    class="kit-tabs__tab"
    :class="{ 'is-active': aktiv, disabled: props.disabled }"
    :value="props.value"
    :disabled="props.disabled"
  >
    <slot />
  </TabsTrigger>
</template>
