<template>
    <li v-if="href === '#'" :class="['breadcrumb-item active', addClass]" aria-current="page"><slot></slot></li>
    <li v-else :class="['breadcrumb-item', addClass]"><a :href="href"><slot></slot></a></li>

</template>

<script>
    export default {
        props: {
            href: {
                type: String,
                default: '#',
            },
            addClass: {
                default: '',
            },
        }
    }
</script>
