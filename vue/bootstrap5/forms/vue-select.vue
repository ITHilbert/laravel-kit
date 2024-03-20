<template>
    <select :id="name" :name="name" :value="value" :class="['form-control combobox', $attrs.class, addClass]"><slot></slot></select>
</template>


<script>
export default{
    props: {
        'name': {
            required: true,
            type: String,
        },
        'value': {
            required: true,
        },
        addClass: {
            default: '',
        },
    }
}
</script>
