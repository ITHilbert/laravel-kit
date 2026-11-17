<script setup lang="ts">
// Tabs auf Reka UI. Die <kit-tab>-Kinder (auch in einem Wrapper-Element) landen in der Liste
// (.kit-tabs__list, role="tablist"), alle übrigen Kinder (die Panels) stehen danach.
// Zustand: v-model (modelValue) oder default-value; ohne beides ist kein Tab aktiv.
import { Comment, Fragment, type FunctionalComponent, type VNode, useSlots } from 'vue'
import { TabsList, TabsRoot } from 'reka-ui'
import KitTab from './tab.vue'

defineOptions({ name: 'KitTabs' })

const props = defineProps<{
  defaultValue?: string
  orientation?: 'horizontal' | 'vertical'
}>()

const modell = defineModel<string>({ default: undefined })

const slots = useSlots()

const flach = (knoten: VNode[]): VNode[] =>
  knoten.flatMap((k) => (k.type === Fragment && Array.isArray(k.children) ? flach(k.children as VNode[]) : [k]))

/** Alle KitTab-Knoten unterhalb von k (Elemente mit Array-Kindern werden durchsucht). */
function tabsIn(k: VNode): VNode[] {
  if (k.type === KitTab) return [k]
  if (typeof k.type === 'string' && Array.isArray(k.children)) return flach(k.children as VNode[]).flatMap(tabsIn)
  return []
}

function teile(): { tabs: VNode[]; rest: VNode[] } {
  const kinder = flach(slots.default?.() ?? []).filter((k) => k.type !== Comment)
  const tabs: VNode[] = []
  const rest: VNode[] = []
  for (const k of kinder) {
    const gefunden = tabsIn(k)
    if (gefunden.length > 0) tabs.push(...gefunden)
    else rest.push(k)
  }
  return { tabs, rest }
}

const KitKnoten: FunctionalComponent<{ knoten: VNode[] }> = (p) => p.knoten
</script>

<template>
  <TabsRoot
    v-model="modell"
    class="kit-tabs"
    :default-value="props.defaultValue"
    :orientation="props.orientation"
  >
    <TabsList class="kit-tabs__list"><KitKnoten :knoten="teile().tabs" /></TabsList>
    <KitKnoten :knoten="teile().rest" />
  </TabsRoot>
</template>
