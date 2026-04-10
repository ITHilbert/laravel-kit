<template>
    <pre class="code-block language-html" style="max-height:520px" tabindex="0" v-bind="$attrs">
        <code class="language-html"><slot></slot></code>
    </pre>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
