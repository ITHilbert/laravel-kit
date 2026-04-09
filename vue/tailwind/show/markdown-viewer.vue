<template>
  <div class="markdown-body prose prose-indigo max-w-none font-sans prose-headings:font-sans prose-headings:text-blue-900 prose-headings:font-bold text-gray-800 leading-relaxed dark:prose-invert" v-html="compiledMarkdown"></div>
</template>

<script>
import { marked } from 'marked';
import DOMPurify from 'dompurify';

export default {
  name: 'MarkdownViewer',
  props: {
    content: {
      type: String,
      required: true,
      default: ''
    }
  },
  computed: {
    compiledMarkdown() {
      if (!this.content) return '';
      try {
        const rawHtml = marked.parse(this.content);
        return DOMPurify.sanitize(rawHtml);
      } catch (e) {
        console.error('Error parsing markdown:', e);
        return '<p class="text-red-500">Error rendering content.</p>';
      }
    }
  }
}
</script>


