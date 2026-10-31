<template>
    <div
        ref="modal"
        class="dialog-overlay"
        :class="{ hidden: !offen }"
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
import { onMounted, onUnmounted, ref } from 'vue';
import { loeschRoute, onLoeschen } from '../../../js/loeschen.js';

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

const modal = ref<HTMLElement | null>(null);
const offen = ref(false);
const aktuelleRoute = ref('');
let abmelden: (() => void) | null = null;

function close() {
    offen.value = false;
}

function confirm() {
    close();
    if (!aktuelleRoute.value) return;
    const form = document.getElementById('formDelete') as HTMLFormElement | null;
    if (form) {
        form.setAttribute('action', aktuelleRoute.value);
        form.submit();
    }
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') {
        close();
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    // Ein Klick auf button-delete (data-kit-delete) meldet die ID über das Kit-Ereignis.
    abmelden = onLoeschen(({ id }) => {
        aktuelleRoute.value = loeschRoute(props.route, id);
        offen.value = true;
    });
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    abmelden?.();
});
</script>
