<template v-slot="default">
    <textarea class="form-control" :rows="rows" v-model="currentValue"></textarea>
</template>



<script>
    export default {
        props: {
            'rows': {
                default: '3'
            },
        }    
    }
</script>

<script>
    export default {
        props: {
            'rows': {
                default: '3'
            },
            value: {
                required: false,
                type: String,
                default: '',
            },
        },

        data() {
            return {
                currentValue: this.getDefaultValue()
            }
        },

        methods: {
            getDefaultValue() {
                if (this.$slots.default && this.$slots.default.length) {
                    return this.$slots.default[0].text
                }

                return this.value
            }
        },
    }
</script>