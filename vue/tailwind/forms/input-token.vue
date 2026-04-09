<template>
    <div :class="['input-token flex items-center w-full rounded-md border border-gray-300 bg-white transition-colors focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 dark:bg-gray-700 dark:border-gray-600', $attrs.class, addClass]">
        <input 
            :type="showCleartext ? 'text' : 'password'" 
            :id="name" 
            :name="name" 
            :value="value"
            @input="$emit('update:modelValue', $event.target.value)"
            @click.stop
            class="block w-full min-w-0 flex-1 border-0 bg-transparent py-2 pl-3 focus:ring-0 sm:text-sm dark:text-white" 
            v-bind="$attrs"
            ref="inputField"
        >
        <div class="flex items-center pr-2 space-x-1 shrink-0">
            <button type="button" @click.stop="toggleVisibility" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors focus:outline-none" title="Anzeigen/Verbergen">
                <i :class="showCleartext ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
            <button type="button" @click.stop="copyToClipboard" class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-600 rounded transition-colors focus:outline-none" title="Kopieren">
                <i :class="copied ? 'fas fa-check text-green-500' : 'fas fa-copy'"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    inheritAttrs: false,
    props: {
        addClass: {
            default: '',
        },
        name: {
            type: String,
            default: '',
        },
        value: {
            type: String,
            default: ''
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            showCleartext: false,
            copied: false
        }
    },
    methods: {
        toggleVisibility() {
            this.showCleartext = !this.showCleartext;
        },
        copyToClipboard() {
            const textToCopy = this.value || this.$refs.inputField.value;
            
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
            
            this.copied = true;
            setTimeout(() => {
                this.copied = false;
            }, 2000);
        }
    }
}
</script>
