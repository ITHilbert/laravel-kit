<script setup lang="ts">
// Formular mit CSRF-Feld. Props: method (get, post, put, patch, delete), übrige Attribute (action …) gehen ans form-Element.
// put/patch/delete senden per post mit verstecktem _method; bei get gibt es weder _token noch _method.
import { computed } from 'vue'

defineOptions({ name: 'KitForm' })

const props = withDefaults(defineProps<{ method?: string }>(), { method: 'post' })

const art = computed(() => props.method.toLowerCase())
const browserMethode = computed(() => (art.value === 'get' ? 'get' : 'post'))
const nachgebildet = computed(() => (['put', 'patch', 'delete'].includes(art.value) ? art.value.toUpperCase() : null))
const token = computed(() => {
  if (art.value === 'get' || typeof document === 'undefined') return null
  return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? null
})
</script>

<template>
  <form class="kit-form" :method="browserMethode">
    <input v-if="token" type="hidden" name="_token" :value="token" />
    <input v-if="nachgebildet" type="hidden" name="_method" :value="nachgebildet" />
    <slot />
  </form>
</template>
