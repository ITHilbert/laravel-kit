<template>
    <div :value="value" class="show-date">
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
                var tmp = value.split('-');
                return tmp[2] + "." + tmp[1] + "." + tmp[0];
            }
        }
    }
</script>
