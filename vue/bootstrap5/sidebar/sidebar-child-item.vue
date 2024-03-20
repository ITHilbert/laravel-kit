<template>
    <li class="nav-item">
        <a :class="[paneClasses, addClass]" :href="href" data-bs-toggle="" aria-expanded="false" v-bind="$attrs">
            <div class="d-flex align-items-center">
                <span class="nav-link-text">
                    <slot></slot>
                </span>
            </div>
        </a>
    </li>
</template>

<script>
    export default {
        props: {
            'href': {
                type: String,
                default: '#',
            },
            'img': {
                type: String,
                default: 'home',
            },
            'addClass': {
                default: '',
            },
            'active': {
                type: Boolean,
                default: false
            },
            /* Sidebar item auch bei Unterseiten aktivieren */
            'ActiveOnSub': {
                type: Boolean,
                default: true
            }

        },
        data() {
            return {
                isActive: this.active
            }
        },
        computed: {
            paneClasses() {
                return {
                    'nav-link': true,
                    'active': this.isActive
                }
            }
        },
        created() {
            const relativePath = this.href.replace(window.location.origin, '');
            const currentPath = window.location.pathname.replace(/\/$/, ''); // Entfernt das abschließende "/"
            //console.log(currentPath + ' = ' + relativePath);
            if(this.ActiveOnSub === true){
                if (currentPath.startsWith(relativePath)) {
                    this.isActive = true;
                    this.$parent.setParentActive();
                } else {
                    this.isActive = false;
                }
            }else{
                if (currentPath === relativePath) {
                    this.isActive = true;
                    this.$parent.setParentActive();
                } else {
                    this.isActive = false;
                }
            }
        }
    }
</script>
