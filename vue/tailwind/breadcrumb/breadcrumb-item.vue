<template>
    <li v-if="href === '#'" :class="['breadcrumb-item active', addClass]" aria-current="page"
        style="color: #6b7280; font-size: 0.875rem;">
        <slot></slot>
    </li>
    <li v-else :class="['breadcrumb-item', addClass]" style="font-size: 0.875rem;">
        <a :href="href" style="color: #4f46e5; text-decoration: none;"
           onmouseover="this.style.textDecoration='underline'"
           onmouseout="this.style.textDecoration='none'">
            <slot></slot>
        </a>
    </li>
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
