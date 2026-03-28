<template>
    <li class="block">
        <a :class="['block px-6 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded transition-colors px-6', addClass]" :href="href"> <span class="mr-2 text-900" :data-feather="img"></span><span><slot></slot></span></a>
    </li>
</template>


<script>
    export default {
        props: {
            href: {
                type: String,
                default: '#',
            },
            img: {
                type: String,
                default: 'home',
            },
            addClass: {
                default: '',
            }
        }
    }
</script>
