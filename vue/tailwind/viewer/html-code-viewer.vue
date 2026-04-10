<template>
  <pre class="html-code-viewer scrollbar overflow-x-auto p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-mono text-gray-800 dark:text-gray-200" v-bind="$attrs">
    <code>{{ debug ? "[debug-mode]\n" : '' }}{{ code }}</code>
  </pre>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, nextTick } from 'vue';

const props = withDefaults(defineProps<{
    debug?: boolean;
    el: string | HTMLElement;
}>(), {
    debug: false
});

defineOptions({ inheritAttrs: false });

const elementRef = ref<HTMLElement | null>(null);
const code = ref('');

function _mountRef() {
    const _el = props.el;
    const el = typeof _el === 'string' ? window.document.querySelector(_el) as HTMLElement : _el;
    elementRef.value = el;
}

function _parser(htmlStr: string) {
    return [...htmlStr.replace(/^((\s|\n|\r|\n\r|\t))+/g, '')].reduce((accum: string[], s: string) => {
        if (s === '<'){
            s = "\n" + s;
        } else if (s === '>'){
            s = s + "\n";
        }
        accum.push(s);
        return accum;
    }, []).join('').replace(/(\n|\r|\r\n){1,}/gm, "\n");
}

function updatePreCode() {
    if (!elementRef.value) return;
    code.value = _parser(elementRef.value.innerHTML);
}

watch(() => props.el, () => {
    _mountRef();
}, { deep: true });

watch(elementRef, () => {
    updatePreCode();
}, { deep: true });

onMounted(() => {
    nextTick(() => {
        _mountRef();
    });
});
</script>