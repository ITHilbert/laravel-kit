<template>
    <div class="nav-item-wrapper">
        <a class="nav-link dropdown-indicator label-1" :href="href" role="button" data-bs-toggle="collapse" :aria-expanded="isActive" :aria-controls="name" v-bind="$attrs">
            <div class="d-flex align-items-center">
                <div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div><span class="nav-link-icon"><span :data-feather="img"></span></span><span class="nav-link-text">{{ title }}</span>
            </div>
        </a>
        <div class="parent-wrapper label-1">
            <ul :class="ulClasses" data-bs-parent="#navbarVerticalCollapse" :id="name">
                <li class="collapsed-nav-item-title d-none">
                   {{ title }}
                </li>
                <slot></slot>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                default: 'home',
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
            const name = this.title ? this.title.replace(/ /g, '-') : ''; // Prüfen, ob der this.title Wert nicht null oder undefined ist
            const href = name ? `#${name}` : ''; // Wenn name vorhanden ist, erstelle href

            return {
                isActive: this.active,
                name: name,
                href: href
            }
        },
        computed: {
            paneClasses() {
                return {
                    'label-1': true,
                    'nav-link': true,
                    'active': this.isActive
                }
            },
            ulClasses() {
                return {
                    'nav': true,
                    'collapse': true,
                    'parent': true,
                    'show': this.isActive
                }
            }
        },
        methods: {
            setParentActive() {
                this.isActive = true;
            }
        }
    }
</script>
