<template >
    <div class="input-group">
        <input type="password" :id="name" :name="name" :class="['form-control input-password', $attrs.class, addClass]" v-bind="$attrs">
        <span class="input-group-text"><span data-feather="key"></span></span>
    </div>
</template>

<script>
    export default {
        props: {
            addClass: {
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>
