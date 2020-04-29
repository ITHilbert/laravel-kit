<template >
    <div class="input-group">
        <input :type="type" :class="defaultclass + addclass" aria-label="Euro" :value="formatValue(value)" v-bind="$attrs">
        <div class="input-group-append">
            <span class="input-group-text"> &euro; </span>
        </div>
    </div>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'type': {
                default: 'text'
                },
            'addclass': {
                default: ''
            },
            'value': {}
        },
        data: function(){
            return {
                'defaultclass': 'form-control input-euro ',
            }
        },
        methods:{
            formatValue(value) {
                let val = (value/1).toFixed(2).replace('.', ',')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
        }
    }
</script>