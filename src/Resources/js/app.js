/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

///*****v-select components
// * https://vue-select.org/ *****/
//import vselect from 'vue-select';
//Vue.component('v-select', vselect);
//import "vue-select/src/scss/vue-select.scss";
//
///*****vue-js-toggle-button
// * https://github.com/euvl/vue-js-toggle-button *****/
//import { ToggleButton } from 'vue-js-toggle-button';
//Vue.component('ToggleButton', ToggleButton);



/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('card', require('./components/Card.vue').default);
Vue.component('hform', require('./components/HForm.vue').default);
Vue.component('group', require('./components/group.vue').default);

//Comobobox
Vue.component('combobox', require('./components/input/combobox.vue').default);
Vue.component('cb', require('./components/input/cb.vue').default);

//Input
Vue.component('input-text', require('./components/input/input-text.vue').default);
Vue.component('input-email', require('./components/input/input-email.vue').default);
Vue.component('input-euro', require('./components/input/input-euro.vue').default);
Vue.component('input-percent', require('./components/input/input-percent.vue').default);
Vue.component('input-int', require('./components/input/input-int.vue').default);
Vue.component('input-date', require('./components/input/input-date.vue').default);
Vue.component('input-password', require('./components/input/input-password.vue').default);
Vue.component('input-number', require('./components/input/input-number.vue').default);
Vue.component('text-area', require('./components/input/text-area.vue').default);
Vue.component('checkbox', require('./components/input/checkbox.vue').default);
Vue.component('radiobox', require('./components/input/radiobox.vue').default);
Vue.component('input-hidden', require('./components/input/input-hidden.vue').default);
Vue.component('hlabel', require('./components/input/hlabel.vue').default);


//Buttons
Vue.component('hbutton', require('./components/buttons/HButton.vue').default);
Vue.component('button-create', require('./components/buttons/button-create.vue').default);
Vue.component('button-show', require('./components/buttons/button-show.vue').default);
Vue.component('button-edit', require('./components/buttons/button-edit.vue').default);
Vue.component('button-delete', require('./components/buttons/button-delete.vue').default);
Vue.component('button-save', require('./components/buttons/button-save.vue').default);
Vue.component('button-back', require('./components/buttons/button-back.vue').default);
Vue.component('button-submit', require('./components/buttons/button-submit.vue').default);
Vue.component('button-cancel', require('./components/buttons/button-cancel.vue').default);
Vue.component('button-reset', require('./components/buttons/button-reset.vue').default);


//Show
Vue.component('txt', require('./components/show/text.vue').default);
Vue.component('euro', require('./components/show/euro.vue').default);
Vue.component('datum', require('./components/show/date.vue').default);

//Dialoge
Vue.component('dialog-delete', require('./components/dialog/delete.vue').default);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#vue-app',
});
