/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//window.Vue = require('vue');
import Vue from 'vue'

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
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


//Buttons
Vue.component('hbutton', require('@ithilbert/jlib/src/vue/buttons/HButton.vue').default);
Vue.component('button-create', require("@ithilbert/jlib/src/vue/buttons/button-create.vue").default);
Vue.component('button-show', require('@ithilbert/jlib/src/vue/buttons/button-show.vue').default);
Vue.component('button-edit', require('@ithilbert/jlib/src/vue/buttons/button-edit.vue').default);
Vue.component('button-delete', require('@ithilbert/jlib/src/vue/buttons/button-delete.vue').default);
Vue.component('button-save', require('@ithilbert/jlib/src/vue/buttons/button-save.vue').default);
Vue.component('button-back', require('@ithilbert/jlib/src/vue/buttons/button-back.vue').default);
Vue.component('button-submit', require('@ithilbert/jlib/src/vue/buttons/button-submit.vue').default);
Vue.component('button-cancel', require('@ithilbert/jlib/src/vue/buttons/button-cancel.vue').default);
Vue.component('button-reset', require('@ithilbert/jlib/src/vue/buttons/button-reset.vue').default);

//Card
Vue.component('card-body', require("@ithilbert/jlib/src/vue/card/card-body.vue").default);
Vue.component('card-columns', require("@ithilbert/jlib/src/vue/card/card-columns.vue").default);
Vue.component('card-deck', require("@ithilbert/jlib/src/vue/card/card-deck.vue").default);
Vue.component('card-footer', require("@ithilbert/jlib/src/vue/card/card-footer.vue").default);
Vue.component('card-group', require("@ithilbert/jlib/src/vue/card/card-group.vue").default);
Vue.component('card-header', require("@ithilbert/jlib/src/vue/card/card-header.vue").default);
Vue.component('card-img-bottom', require("@ithilbert/jlib/src/vue/card/card-img-bottom.vue").default);
Vue.component('card-img-top', require("@ithilbert/jlib/src/vue/card/card-img-top.vue").default);
Vue.component('card-text', require("@ithilbert/jlib/src/vue/card/card-text.vue").default);
Vue.component('card-title', require("@ithilbert/jlib/src/vue/card/card-title.vue").default);
Vue.component('card', require("@ithilbert/jlib/src/vue/card/card.vue").default);
//Vue.component('jCard', require("@ithilbert/jlib/src/vue/card/jCard.vue").default);

//Dialoge
Vue.component('dialog-delete', require('@ithilbert/jlib/src/vue/dialog/delete.vue').default);

//Input
Vue.component('input-text', require('@ithilbert/jlib/src/vue/input/input-text.vue').default);
Vue.component('input-email', require('@ithilbert/jlib/src/vue/input/input-email.vue').default);
Vue.component('input-euro', require('@ithilbert/jlib/src/vue/input/input-euro.vue').default);
Vue.component('input-percent', require('@ithilbert/jlib/src/vue/input/input-percent.vue').default);
Vue.component('input-int', require('@ithilbert/jlib/src/vue/input/input-int.vue').default);
Vue.component('input-date', require('@ithilbert/jlib/src/vue/input/input-date.vue').default);
Vue.component('input-password', require('@ithilbert/jlib/src/vue/input/input-password.vue').default);
Vue.component('input-number', require('@ithilbert/jlib/src/vue/input/input-number.vue').default);
Vue.component('text-area', require('@ithilbert/jlib/src/vue/input/text-area.vue').default);
Vue.component('checkbox', require('@ithilbert/jlib/src/vue/input/checkbox.vue').default);
Vue.component('radiobox', require('@ithilbert/jlib/src/vue/input/radiobox.vue').default);
Vue.component('input-hidden', require('@ithilbert/jlib/src/vue/input/input-hidden.vue').default);
Vue.component('hlabel', require('@ithilbert/jlib/src/vue/input/hlabel.vue').default);
//Vue.component('editor', require('./editor.vue').default);

//Comobobox
Vue.component('combobox', require('@ithilbert/jlib/src/vue/input/combobox.vue').default);
Vue.component('search', require('@ithilbert/jlib/src/vue/input/search.vue').default);


//Show
Vue.component('txt', require('@ithilbert/jlib/src/vue/show/show-text.vue').default);
Vue.component('euro', require('@ithilbert/jlib/src/vue/show/show-euro.vue').default);
Vue.component('datum', require('@ithilbert/jlib/src/vue/show/show-date.vue').default);

//SVG
Vue.component('svg-pen', require('@ithilbert/jlib/src/vue/svg/pen.vue').default);
Vue.component('svg-star', require('@ithilbert/jlib/src/vue/svg/star.vue').default);

//Tabs
Vue.component('tab-content', require('@ithilbert/jlib/src/vue/tabs/tab-content.vue').default);
Vue.component('tab-header', require('@ithilbert/jlib/src/vue/tabs/tab-header.vue').default);
Vue.component('tab-iteam', require('@ithilbert/jlib/src/vue/tabs/tab-iteam.vue').default);
Vue.component('tab-panel', require('@ithilbert/jlib/src/vue/tabs/tab-panel.vue').default);


//View
Vue.component('rating', require('@ithilbert/jlib/src/vue/view/rating.vue').default);


//Design
Vue.component('hform', require('@ithilbert/jlib/src/vue/HForm.vue').default);
Vue.component('group', require('@ithilbert/jlib/src/vue/group.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#vue-app',
});