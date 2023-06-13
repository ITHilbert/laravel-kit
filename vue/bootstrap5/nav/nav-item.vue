<template>
    <li class="nav-item">
        <a :class="['nav-link px-3', addClass]" :href="href"> <span class="me-2 text-900" :data-feather="img"></span><span><slot></slot></span></a>
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
