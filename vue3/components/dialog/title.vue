<script setup lang="ts">
// Überschrift des Dialogs; Reka verknüpft sie per aria-labelledby mit dem Inhalt.
import { DialogTitle } from 'reka-ui'

defineOptions({ name: 'KitDialogTitle' })
</script>

<template>
  <DialogTitle class="kit-dialog__title"><slot /></DialogTitle>
</template>
