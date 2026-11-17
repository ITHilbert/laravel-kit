<script setup lang="ts">
// Inhalt eines Tabs; Reka rendert ihn nur, solange der Tab aktiv ist.
import { TabsContent } from 'reka-ui'

defineOptions({ name: 'KitTabPanel' })

const props = defineProps<{ value: string }>()
</script>

<template>
  <TabsContent class="kit-tabs__panel is-active" :value="props.value"><slot /></TabsContent>
</template>
