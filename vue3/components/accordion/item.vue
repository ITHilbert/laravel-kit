<script setup lang="ts">
// Ein Eintrag des Accordions: Titel als Trigger, Slot als Inhalt. Zustände: is-open, disabled.
import { computed } from 'vue'
import { AccordionContent, AccordionHeader, AccordionItem, AccordionTrigger, injectAccordionRootContext } from 'reka-ui'

defineOptions({ name: 'KitAccordionItem' })

const props = defineProps<{ value: string; title?: string; disabled?: boolean }>()

const wurzel = injectAccordionRootContext()
const offen = computed(() => {
  const wert = wurzel.modelValue.value
  return Array.isArray(wert) ? wert.includes(props.value) : wert === props.value
})
</script>

<template>
  <AccordionItem
    class="kit-accordion__item"
    :class="{ 'is-open': offen, disabled: props.disabled }"
    :value="props.value"
    :disabled="props.disabled"
  >
    <AccordionHeader as="div">
      <AccordionTrigger class="kit-accordion__trigger">{{ props.title }}</AccordionTrigger>
    </AccordionHeader>
    <AccordionContent class="kit-accordion__content"><slot /></AccordionContent>
  </AccordionItem>
</template>
