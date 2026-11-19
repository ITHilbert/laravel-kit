<script setup lang="ts">
// Accordion auf Reka UI. Zustand: v-model (modelValue) oder default-value.
import { AccordionRoot } from 'reka-ui'

defineOptions({ name: 'KitAccordion' })

const props = withDefaults(
  defineProps<{
    type?: 'single' | 'multiple'
    collapsible?: boolean
    defaultValue?: string | string[]
  }>(),
  { type: 'single', collapsible: false, defaultValue: undefined },
)

const modell = defineModel<string | string[]>()
</script>

<template>
  <AccordionRoot
    v-model="modell"
    class="kit-accordion"
    :type="props.type"
    :collapsible="props.collapsible"
    :default-value="props.defaultValue"
  >
    <slot />
  </AccordionRoot>
</template>
