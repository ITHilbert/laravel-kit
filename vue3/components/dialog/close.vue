<script setup lang="ts">
// Schaltfläche, die den Dialog schließt; der Fokus geht danach an den Trigger zurück.
import { DialogClose } from 'reka-ui'

defineOptions({ name: 'KitDialogClose' })
</script>

<template>
  <DialogClose class="kit-dialog__close"><slot /></DialogClose>
</template>
