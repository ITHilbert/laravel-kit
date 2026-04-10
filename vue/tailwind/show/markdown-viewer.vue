<template>
  <div class="markdown-body prose prose-indigo max-w-none font-sans md:prose-lg prose-headings:font-sans prose-headings:text-blue-900 prose-headings:font-bold text-gray-800 leading-relaxed dark:prose-invert" v-html="compiledMarkdown" v-bind="$attrs"></div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const props = withDefaults(defineProps<{
    content?: string;
}>(), {
    content: ''
});

defineOptions({ inheritAttrs: false });

const compiledMarkdown = computed(() => {
    if (!props.content) return '';
    try {
        const rawHtml = marked.parse(props.content) as string;
        return DOMPurify.sanitize(rawHtml);
    } catch (e) {
        console.error('Error parsing markdown:', e);
        return '<p class="text-red-500">Error rendering content.</p>';
    }
});
</script>
