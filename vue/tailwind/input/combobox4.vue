<template>
    <v-select :label="label" :options="data"  v-model="select" :value="value"><slot></slot></v-select>
</template>

<script>
/*****v-select components
 https://vue-select.org/ *****/
import vSelect from 'vue-select';
import "vue-select/src/scss/vue-select.scss";

export default {
    props: {
        'data': {},
        'label': {default: 'cbOptionCaption'},
        'value': {default: 1},
    },
    components: {
        'v-select': vSelect
    },
  
    data() {
        return {
            select: [],
        };
    }, 
}
</script>
