<template>
    <div
        id="myModalDelete"
        :route="route"
        class="dialog-overlay hidden"
        @click.self="close"
    >
        <div class="dialog-content">
            <!-- Header -->
            <div class="dialog-header">
                <h5 class="dialog-title">{{ titel }}</h5>
                <button
                    type="button"
                    class="dialog-close-btn"
                    @click="close"
                    aria-label="Schließen"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Body -->
            <div class="dialog-body">
                <div class="dialog-icon-container">
                    <svg class="dialog-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.834-1.964-.834-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <p class="dialog-text">{{ body }}</p>
            </div>
            <!-- Footer -->
            <div class="dialog-footer">
                <button
                    id="DialogDeleteButton1"
                    type="button"
                    class="dialog-btn-cancel"
                    @click="close"
                >
                    {{ btn1text }}
                </button>
                <button
                    id="DialogDeleteButton2"
                    type="button"
                    class="dialog-btn-confirm"
                    @click="confirm"
                >
                    {{ btn2text }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';

const props = withDefaults(defineProps<{
    titel?: string;
    body?: string;
    btn1text?: string;
    btn2text?: string;
    route?: string;
}>(), {
    titel: 'Löschen',
    body: 'Wollen Sie den Datensatz wirklich löschen?',
    btn1text: 'Abbruch',
    btn2text: 'Löschen',
    route: ''
});

defineOptions({ inheritAttrs: false });

function close() {
    const modal = document.getElementById('myModalDelete');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function confirm() {
    close();
    const modal = document.getElementById('myModalDelete');
    if (!modal) return;
    
    let pfad = modal.getAttribute('route');
    if (pfad) {
        // @ts-ignore - deleteID comes from global scope typically in this specific app
        if (typeof deleteID !== 'undefined') {
            pfad = pfad.substring(0, pfad.lastIndexOf('/') + 1) + deleteID;
        }
        const form = document.getElementById('formDelete') as HTMLFormElement;
        if (form) {
            form.setAttribute('action', pfad);
            form.submit();
        }
    }
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') {
        close();
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>