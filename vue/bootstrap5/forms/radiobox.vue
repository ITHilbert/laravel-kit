<template >
   <div class="form-check">
        <input type="radio" :id="id" :class="['form-check-input radiobox', $attrs.class, addClass]" v-bind="$attrs" :checked="active">
        <label class="form-check-label" :for="id"><slot></slot></label>
    </div>

</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            id: {
                type: String,
                default: ''
            },
            active: {
                type: Boolean,
                default: false
            },
            addClass: {
                type: String,
                default: '',
            },
        },
    }
</script>
