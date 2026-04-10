<template>
    <div class="relative flex items-center w-full">
        <input 
            :type="showCleartext ? 'text' : 'password'" 
            :id="name" 
            :name="name" 
            :value="value"
            @input="handleInput"
            @click.stop
            class="input-token pr-16" 
            v-bind="$attrs"
            ref="inputField"
        >
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
            <button type="button" @click.stop="showCleartext = !showCleartext" class="p-1.5 text-gray-400 hover:text-gray-700 focus:outline-none" title="Anzeigen/Verbergen">
                <i :class="showCleartext ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
            <button type="button" @click.stop="copyToClipboard" class="p-1.5 text-gray-400 hover:text-blue-600 focus:outline-none" title="Kopieren">
                <i :class="copied ? 'fas fa-check text-green-500' : 'fas fa-copy'"></i>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
    name?: string;
    value?: string;
}>();

const emit = defineEmits(['update:modelValue']);

defineOptions({ inheritAttrs: false });

const showCleartext = ref(false);
const copied = ref(false);
const inputField = ref<HTMLInputElement | null>(null);

function handleInput(event: Event) {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
}

function copyToClipboard() {
    const textToCopy = props.value || inputField.value?.value || '';
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy);
    } else {
        let textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try { document.execCommand('copy'); } catch (err) {}
        textArea.remove();
    }
    
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
}
</script>
