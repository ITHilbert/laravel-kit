<template >
   <div class="form-check">
        <input type="radio" :id="id" :class="defaultclass + addclass" v-bind="$attrs">
        <label class="form-check-label" :for="id"><slot></slot></label>
    </div>
    
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'id': {},
            'addclass': {}
        },
        data: function(){
            return {
                'defaultclass': 'form-check-input ',
            }
        }
    }
</script>