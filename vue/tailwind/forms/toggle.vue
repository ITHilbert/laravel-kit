<template>
    <button type="button" @click="toggle" :class="{ 'toggle-active': isActive }" :value="isActive">
      {{ isActive ? 'An' : 'Aus' }}
    </button>
</template>

<script>
  export default {
    name: 'toggle',
    data() {
      return {
        isActive: false,
      };
    },
    methods: {
      toggle() {
        this.isActive = !this.isActive;
        this.$emit('toggle', this.isActive);
      },
    },
  };
</script>

<style scoped>
  button {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    padding: 0.25rem 0.5rem;
    transition: background-color 0.2s, color 0.2s;
  }

  .toggle-active {
    background-color: #007bff;
    color: #fff;
  }
</style>
