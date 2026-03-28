<template>
    <select :options="options" :value="value" :class="[defaultclass, $attrs.class, addClass]">
        <slot></slot>
        <template v-if="options != null">
            <template v-if="ocount()">
                <template v-for="option in options">
                    <option v-if="option.cbKey === value"  :value="option.cbKey" selected>{{ option.cbCaption }}</option>
                    <option v-else  :value="option.cbKey" >{{ option.cbCaption }}</option>
                </template>
            </template>
        </template>
    </select>
</template>


<script>
export default{
    props: {
        'options': {
            default: () => ({
                cbKey: {},
                cbCaption: {}
            })
        },
        'value': {
            default: 0
        },
        addClass: {
            default: '',
        },
    },
    data: function () {
        return {
            defaultclass: 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 combobox ',
        };
    },
    methods: {
        //Prüfen ob Daten per options übergeben wurden
        ocount: function () {
            //console.log(typeof this.options);
            if (typeof this.options == 'object' ) {
                //console.log(this.options.length);
                if(this.options.length !== undefined){
                    //console.log('xxx');
                    return true;
                }
            }

            return false;
        }
    },
}
</script>

