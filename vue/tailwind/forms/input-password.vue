<template>
    <div class="relative flex w-full">
        <input
            :type="showPassword ? 'text' : 'password'"
            :id="name"
            :name="name"
            class="input-password pr-12"
            v-bind="$attrs"
        >
        <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 focus:outline-none"
            title="Passwort anzeigen/verbergen"
        >
            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

defineProps<{ name?: string }>();
defineOptions({ inheritAttrs: false });

const showPassword = ref(false);
</script>
