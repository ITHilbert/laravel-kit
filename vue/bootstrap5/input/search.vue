<template>
    <div class="input-group">
    <select :options="options" :value="gewaehlt" @change="geaendert" :class="[defaultclass, $attrs.class, addClass]" v-bind="$attrs">
        <slot></slot>
        <template v-if="options != null">
            <template v-if="ocount()">
                <template v-for="option in options" >
                    <option v-if="option.cbKey === gewaehlt"  :value="option.cbKey" selected>{{ option.cbCaption }}</option>
                    <option v-else  :value="option.cbKey" >{{ option.cbCaption }}</option>
                </template>
            </template>
        </template>
    </select>
    </div>
</template>


<script>
import { initCombobox, aktualisiereCombobox } from '../../../js/combobox.js';

export default{
    props: {
        'options': {
            default: () => ({
                cbKey: {},
                cbCaption: {}
            })
        },
        'value': {
            default: '',
        },
        'addClass': {
            default: '',
        },
    },
    data: function () {
        return {
            defaultclass: 'selectcb ',
            gewaehlt: this.value,
        };
    },
    watch: {
        value: function (neu) {
            this.gewaehlt = neu;
            this.$nextTick(() => aktualisiereCombobox(this.$el));
        },
        options: function () {
            this.$nextTick(() => aktualisiereCombobox(this.$el));
        },
    },
    methods: {
        //Auswahl des Selects übernehmen und melden
        geaendert: function (event) {
            this.gewaehlt = event.target.value;
            this.$emit('input', event.target.value);
        },
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
    mounted () {
        initCombobox(this.$el);
    },
}
</script>



<style>
select {
    display: block;
    width: 100%;
    margin: 0;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 2.2rem 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #ffffff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: inset 0 0 0 rgba(0, 0, 0, 0);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.kit-combobox { display: inline-block; position: relative; margin: 0; margin-right: 1.8em; }

.kit-combobox-input {
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #ffffff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: inset 0 0 0 rgba(0, 0, 0, 0);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.kit-combobox-button {
    position: absolute;
    width: 1.8em;
    margin: 0;
    margin-left: -1px;
    top: 0;
    bottom: 0;
    padding: 0;
    color: #495057;
    background-color: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: 0 0.25rem 0.25rem 0;
    cursor: pointer;
}
.kit-combobox-button::after { content: '\25BE'; }

.kit-combobox-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    max-height: 10em;
    overflow-y: auto;
    overflow-x: hidden;
    margin: 0;
    padding: 0;
    list-style: none;
    background-color: #ffffff;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}
.kit-combobox-list[hidden] { display: none; }
.kit-combobox-list li { padding: 0.25rem 0.75rem; cursor: pointer; }
.kit-combobox-list li:hover,
.kit-combobox-list li.active { color: #ffffff; background-color: #0d6efd; }
</style>
