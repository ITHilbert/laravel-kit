/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//window.Vue = require('vue');
import Vue from 'vue'
import 'livewire-vue'

window.Vue = Vue //this is important! Do not use require('vue')

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./bootstrap4/', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


//Tabs
Vue.component('tabs-header', require('./bootstrap4/ctabs/tabs-header.vue').default);
Vue.component('tab-header', require('./bootstrap4/tabs/tab-header.vue').default);
Vue.component('tabs-body', require('./bootstrap4/tabs/tabs-body.vue').default);
Vue.component('tab-body', require('./bootstrap4/tabs/tab-body.vue').default);

//Buttons
Vue.component('hbutton', require('./bootstrap4/buttons/hbutton.vue').default);
Vue.component('button-create', require("./bootstrap4/buttons/button-create.vue").default);
Vue.component('button-show', require('./bootstrap4/buttons/button-show.vue').default);
Vue.component('button-edit', require('./bootstrap4/buttons/button-edit.vue').default);
Vue.component('button-delete', require('./bootstrap4/buttons/button-delete.vue').default);
Vue.component('button-save', require('./bootstrap4/buttons/button-save.vue').default);
Vue.component('button-back', require('./bootstrap4/buttons/button-back.vue').default);
Vue.component('button-submit', require('./bootstrap4/buttons/button-submit.vue').default);
Vue.component('button-cancel', require('./bootstrap4/buttons/button-cancel.vue').default);
Vue.component('button-reset', require('./bootstrap4/buttons/button-reset.vue').default);

//Card
Vue.component('card-body', require("./bootstrap4/card/card-body.vue").default);
Vue.component('card-columns', require("./bootstrap4/card/card-columns.vue").default);
Vue.component('card-deck', require("./bootstrap4/card/card-deck.vue").default);
Vue.component('card-footer', require("./bootstrap4/card/card-footer.vue").default);
Vue.component('card-bottom', require("./bootstrap4/card/card-bottom.vue").default);
Vue.component('card-group', require("./bootstrap4/card/card-group.vue").default);
Vue.component('card-header', require("./bootstrap4/card/card-header.vue").default);
Vue.component('card-img-bottom', require("./bootstrap4/card/card-img-bottom.vue").default);
Vue.component('card-img-top', require("./bootstrap4/card/card-img-top.vue").default);
Vue.component('card-text', require("./bootstrap4/card/card-text.vue").default);
Vue.component('card-title', require("./bootstrap4/card/card-title.vue").default);
Vue.component('card', require("./bootstrap4/card/card.vue").default);
Vue.component('jcard', require("./bootstrap4/card/jCard.vue").default);

//Dialoge
Vue.component('dialog-delete', require('./bootstrap4/dialog/delete.vue').default);

//Input
Vue.component('input-text', require('./bootstrap4/input/input-text.vue').default);
Vue.component('input-email', require('./bootstrap4/input/input-email.vue').default);
Vue.component('input-euro', require('./bootstrap4/input/input-euro.vue').default);
Vue.component('input-percent', require('./bootstrap4/input/input-percent.vue').default);
Vue.component('input-int', require('./bootstrap4/input/input-int.vue').default);
Vue.component('input-date', require('./bootstrap4/input/input-date.vue').default);
Vue.component('input-password', require('./bootstrap4/input/input-password.vue').default);
Vue.component('input-number', require('./bootstrap4/input/input-number.vue').default);
Vue.component('text-area', require('./bootstrap4/input/text-area.vue').default);
Vue.component('checkbox', require('./bootstrap4/input/checkbox.vue').default);
Vue.component('radiobox', require('./bootstrap4/input/radiobox.vue').default);
Vue.component('input-hidden', require('./bootstrap4/input/input-hidden.vue').default);
Vue.component('hlabel', require('./bootstrap4/input/hlabel.vue').default);
Vue.component('input-file', require('./bootstrap4/input/input-file.vue').default);
Vue.component('input-file-img', require('./bootstrap4/input/input-file-img.vue').default);
//Vue.component('editor', require('./bootstrap4/editor.vue').default);

//Comobobox
Vue.component('combobox', require('./bootstrap4/input/combobox.vue').default);
Vue.component('search', require('./bootstrap4/input/search.vue').default);


//Show
Vue.component('txt', require('./bootstrap4/show/show-text.vue').default);
Vue.component('euro', require('./bootstrap4/show/show-euro.vue').default);
Vue.component('datum', require('./bootstrap4/show/show-date.vue').default);

//SVG
Vue.component('svg-pen', require('./bootstrap4/svg/pen.vue').default);
Vue.component('svg-star', require('./bootstrap4/svg/star.vue').default);

//View
Vue.component('rating', require('./bootstrap4/view/rating.vue').default);


//Design
Vue.component('hform', require('./bootstrap4/HForm.vue').default);
Vue.component('group', require('./bootstrap4/group.vue').default);

//Grid
Vue.component('row', require('./bootstrap4/grid/row.vue').default);
Vue.component('col-1', require('./bootstrap4/grid/col-1.vue').default);
Vue.component('col-2', require('./bootstrap4/grid/col-2.vue').default);
Vue.component('col-3', require('./bootstrap4/grid/col-3.vue').default);
Vue.component('col-4', require('./bootstrap4/grid/col-4.vue').default);
Vue.component('col-5', require('./bootstrap4/grid/col-5.vue').default);
Vue.component('col-6', require('./bootstrap4/grid/col-6.vue').default);
Vue.component('col-7', require('./bootstrap4/grid/col-7.vue').default);
Vue.component('col-8', require('./bootstrap4/grid/col-8.vue').default);
Vue.component('col-9', require('./bootstrap4/grid/col-9.vue').default);
Vue.component('col-10', require('./bootstrap4/grid/col-10.vue').default);
Vue.component('col-11', require('./bootstrap4/grid/col-11.vue').default);
Vue.component('col-12', require('./bootstrap4/grid/col-12.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#vue-app',
});