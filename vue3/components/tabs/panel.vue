<script setup lang="ts">
// Inhalt eines Tabs. Reka lässt inaktive Panels mit `hidden` gemountet;
// is-active steht nur, solange der Tab aktiv ist (Quelle wie in tab.vue: Root-Kontext).
import { computed } from 'vue'
import { TabsContent, injectTabsRootContext } from 'reka-ui'

defineOptions({ name: 'KitTabPanel' })

const props = defineProps<{ value: string }>()

const wurzel = injectTabsRootContext()
const aktiv = computed(() => wurzel.modelValue.value === props.value)
</script>

<template>
  <TabsContent class="kit-tabs__panel" :class="{ 'is-active': aktiv }" :value="props.value"><slot /></TabsContent>
</template>
