<script setup lang="ts">
// Schaltfläche, die den Dialog öffnet. Muss direktes Kind von <kit-dialog> sein (bleibt an ihrem Platz in der Seite).
import { DialogTrigger } from 'reka-ui'

defineOptions({ name: 'KitDialogTrigger' })
</script>

<template>
  <DialogTrigger class="kit-dialog__trigger"><slot /></DialogTrigger>
</template>
