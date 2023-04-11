<template >
    <div class="input-group">
        <input :type="type" :class="defaultclass + addclass" aria-label="Prozent" v-bind="$attrs">
        <div class="input-group-append">
            <span class="input-group-text">%</span>
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
            'addclass': {}
        },
        data: function(){
            return {
                'defaultclass': 'form-control input-percent ',
            }
        },
    }
</script>
