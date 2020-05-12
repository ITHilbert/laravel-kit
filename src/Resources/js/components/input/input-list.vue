<template>
    <input :list="name" :value="value" class="form-control input-list" >
        <datalist :id="name">
            <slot></slot>
            <template v-if="options != null">
                <template v-if="ocount()">
                    <template v-for="option in options">
                        <option v-if="option.cbKey === value"  :value="option.cbKey" selected>{{ option.cbCaption }}</option>
                        <option v-else  :value="option.cbKey" >{{ option.cbCaption }}</option>
                    </template>
                </template>
            </template>
        </datalist>
    </input>
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
        'name': {
            default: 'inputselect'
        }
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

