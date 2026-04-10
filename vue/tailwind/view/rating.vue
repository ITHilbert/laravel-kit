<template>
<fieldset class="rating rating-total" v-bind="$attrs">
    <div class="flex-container">                                                                                              
        <label for="total-5" :class="classes(5)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>               
        <label for="total-4" :class="classes(4)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label> 
        <label for="total-3" :class="classes(3)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>
        <label for="total-2" :class="classes(2)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>
        <label for="total-1" :class="classes(1)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>
    </div>
</fieldset>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    value?: number | string;
    svg?: string;
}>(), {
    value: 0,
    svg: '#pen'
});

defineOptions({ inheritAttrs: false });

const numericValue = computed(() => Number(props.value) || 0);

const getTitle = computed(() => {
    var val = Math.round(numericValue.value);
    if(val == 1) return 'Einer von Fünf Stiften';
    if(val == 2) return 'Zwei von Fünf Stiften';
    if(val == 3) return 'Drei von Fünf Stiften';
    if(val == 4) return 'Vier von Fünf Stiften';
    if(val == 5) return 'Fünf von Fünf Stiften';
    return 'Null von Fünf Stiften';
});

function classes(nr: number) {
    if(Math.round(numericValue.value) >= nr){
        return 'rated';
    }
    return '';
}
</script>

<style scoped>
svg {
  max-width: 100%;
  max-height: 100%;
  fill: currentColor;
  opacity: 0.2;
}

.flex-container {
  display: flex;
  flex-direction: row-reverse;
  flex-wrap: wrap;
  justify-content: center;
}

.rating {
  margin-bottom: 20px;
  border: none;
}

.rating label {
  position: relative;
  width: 2em;
  height: 2em;
}

.rating-total svg {
  fill: currentColor;
  opacity: 0.2;
}

.rating-total .rated svg {
  fill: gold;
  opacity: 1;
}

@media (max-width: 768px) {
  .feedback {
    padding: 20px 20px 40px 20px;
    margin: 20px auto;
    max-width: 90%;
  }
}

@media (min-width: 768px) and (max-width: 1200px) {
  .feedback {
    padding: 30px 30px 40px 30px;
    margin: 50px auto;
    max-width: 70%;
  }
}
</style>
