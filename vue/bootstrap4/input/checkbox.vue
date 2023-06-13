<template >
   <div class="form-check">
        <input v-if="check() === true" type="checkbox" :value="value" :class="defaultclass + addclass" v-bind="$attrs" checked>
        <input v-else type="checkbox" :value="value" :class="defaultclass + addclass" v-bind="$attrs">
        <label class="form-check-label" :for="id"><slot></slot></label>
    </div>
    
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'id': {},
            'addclass': {default: ''},
            'checked': {default: false},
            'value': {default: 1},
        },
        data: function(){
            return {
                'defaultclass': 'form-check-input ',

            }
        },
        methods: {
            check: function () {
                if(this.checked == 1 || this.checked == true){
                    return true;
                }else{
                    return false;
                }
            }
        }     
    }
</script>