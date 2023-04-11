<template>
    <div class="show-txt">
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
