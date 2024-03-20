<template>
    <div :value="value" class="show-euro">
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
