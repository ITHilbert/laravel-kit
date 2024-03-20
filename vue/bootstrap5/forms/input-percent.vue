<template >
    <div class="input-group">
        <input type="number" :id="name" :name="name" :class="['form-control input-percent', $attrs.class, addClass]" aria-label="Prozent" :step="step" v-bind="$attrs">
        <span class="input-group-text">%</span>
    </div>
</template>


<script>
    export default {
        inheritAttrs: false,
        props: {
            step: {
                type: Number,
                default: 0.01,
            },
            addclass: {
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>
