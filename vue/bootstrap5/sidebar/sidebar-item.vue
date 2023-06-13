<template>
    <div class="nav-item-wrapper">
        <a :class="[paneClasses, addClass]" :href="href" role="button" data-bs-toggle="" aria-expanded="false" v-bind="$attrs">
            <div class="d-flex align-items-center">
                <span class="nav-link-icon">
                    <span :data-feather="img"></span>
                </span>
                <span class="nav-link-text-wrapper">
                    <span class="nav-link-text">
                        <slot></slot>
                    </span>
                </span>
            </div>
        </a>
    </div>
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
            },
            'active': {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                isActive: this.active
            }
        },
        computed: {
            paneClasses() {
                return {
                    'label-1': true,
                    'nav-link': true,
                    'active': this.isActive
                }
            }
        },
        created() {
            const relativePath = this.href.replace(window.location.origin, '');
            const currentPath = window.location.pathname.replace(/\/$/, ''); // Entfernt das abschließende "/"
            //console.log(currentPath + ' = ' + relativePath);
            if (currentPath === relativePath) {
                this.isActive = true;
            } else {
                this.isActive = false;
            }
        }
    }
</script>
