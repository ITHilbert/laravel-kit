<template >
   <div class="form-check">
        <input type="radio" :id="id" :class="[defaultclass, $attrs.class, addClass]" v-bind="$attrs">
        <label class="form-check-label" :for="id"><slot></slot></label>
    </div>

</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'id': {},
                addClass: {
                default: '',
            },
        },
        data: function(){
            return {
                'defaultclass': 'form-check-input radiobox ',
            }
        }
    }
</script>
