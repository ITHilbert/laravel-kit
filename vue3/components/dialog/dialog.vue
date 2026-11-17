<script setup lang="ts">
// Dialog (modal) auf Reka UI. <kit-dialog-trigger> als direktes Kind bleibt in der Seite,
// alle anderen Kinder bilden den Inhalt, der per Teleport an body rendert.
// Zustand: v-model:open (ohne v-model intern, Startwert über das Prop open).
// Attribute an <kit-dialog> gehen an den Inhalt (.kit-dialog__content).
import { Comment, Fragment, type FunctionalComponent, type VNode, useSlots } from 'vue'
import { DialogContent, DialogOverlay, DialogPortal, DialogRoot } from 'reka-ui'
import KitDialogTrigger from './trigger.vue'

defineOptions({ name: 'KitDialog', inheritAttrs: false })

const offen = defineModel<boolean>('open', { default: false })

const slots = useSlots()

/** Kinder des Default-Slots flach (v-for und <template> erzeugen Fragmente), ohne Kommentarknoten. */
function kinder(): VNode[] {
  const flach = (knoten: VNode[]): VNode[] =>
    knoten.flatMap((k) => (k.type === Fragment && Array.isArray(k.children) ? flach(k.children as VNode[]) : [k]))

  return flach(slots.default?.() ?? []).filter((k) => k.type !== Comment)
}

const istTrigger = (k: VNode): boolean => k.type === KitDialogTrigger
const ausserhalb = (): VNode[] => kinder().filter(istTrigger)
const innerhalb = (): VNode[] => kinder().filter((k) => !istTrigger(k))

const KitKnoten: FunctionalComponent<{ knoten: VNode[] }> = (props) => props.knoten
</script>

<template>
  <DialogRoot v-model:open="offen">
    <KitKnoten :knoten="ausserhalb()" />
    <DialogPortal to="body">
      <DialogOverlay class="kit-dialog__overlay" />
      <!-- aria-describedby leer: der Kern hat keine Beschreibung, sonst warnt Reka bei jedem Öffnen. -->
      <DialogContent
        class="kit-dialog__content"
        :class="{ 'is-open': offen }"
        :aria-describedby="undefined"
        v-bind="$attrs"
      >
        <KitKnoten :knoten="innerhalb()" />
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>
