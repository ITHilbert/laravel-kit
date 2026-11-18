<script setup lang="ts">
// Beschriftung eines Feldes. Attribute (z. B. for) gehen ans label-Element.
defineOptions({ name: 'KitLabel' })
</script>

<template>
  <label class="kit-label"><slot /></label>
</template>
