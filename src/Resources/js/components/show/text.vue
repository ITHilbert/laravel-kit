<template>
    <div style="padding-top: 8px;">
        {{ value }}
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
            'value': {
                default: ''
            }
        },
    }
</script>
