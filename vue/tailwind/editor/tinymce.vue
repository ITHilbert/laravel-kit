<template>
    <editor
        api-key="no-api-key"
        :init="{
            height: 500,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left text-gray-500 dark:text-gray-400 paste code help wordcount'
            ],
            toolbar:
                'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
        }"
        v-bind="$attrs"
    ></editor>
</template>

<script setup lang="ts">
import Editor from '@tinymce/tinymce-vue'

defineOptions({ inheritAttrs: false });
</script>
