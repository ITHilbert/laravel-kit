<template >
    <div class="input-group">
        <input :type="type" :class="[defaultclass, $attrs.class, addClass]" aria-label="Prozent" :step="step" v-bind="$attrs">
        <span class="input-group-text">%</span>
    </div>
</template>


<script>
    export default {
        inheritAttrs: false,
        props: {
            'type': {
                default: 'number'
            },
            step: {
                type: Number,
                default: 0.01,
            },
            'addclass': {
                default: '',
            },
        },
        data: function(){
            return {
                'defaultclass': 'form-control input-percent ',
            }
        },
    }
</script>
