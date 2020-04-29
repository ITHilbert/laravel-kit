<template>
    <div :value="value" style="padding-top: 8px;">
        {{ formatValue(value) }}
    </div>
</template>


<script>
    export default {
        props: {
            'value': {
                default: ''
            }
        },
        methods:{
            formatValue(value) {
                let val = (value/1).toFixed(2).replace('.', ',');
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ' €';
            }
        }
    }
</script>
