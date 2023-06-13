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

// const files = require.context('./bootstrap5/', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


//Accordion
Vue.component('accordion', require('./bootstrap5/accordion/accordion.vue').default);
Vue.component('accordion-body', require('./bootstrap5/accordion/accordion-body.vue').default);
Vue.component('accordion-header', require('./bootstrap5/accordion/accordion-header.vue').default);
Vue.component('accordion-item', require('./bootstrap5/accordion/accordion-item.vue').default);

//Breadcrumb
Vue.component('breadcrumb', require('./bootstrap5/breadcrumb/breadcrumb.vue').default);
Vue.component('breadcrumb-item', require('./bootstrap5/breadcrumb/breadcrumb-item.vue').default);


//Buttons
Vue.component('hbutton', require('./bootstrap5/buttons/hbutton.vue').default);
Vue.component('button-create', require("./bootstrap5/buttons/button-create.vue").default);
Vue.component('button-show', require('./bootstrap5/buttons/button-show.vue').default);
Vue.component('button-edit', require('./bootstrap5/buttons/button-edit.vue').default);
Vue.component('button-delete', require('./bootstrap5/buttons/button-delete.vue').default);
Vue.component('button-save', require('./bootstrap5/buttons/button-save.vue').default);
Vue.component('button-back', require('./bootstrap5/buttons/button-back.vue').default);
Vue.component('button-submit', require('./bootstrap5/buttons/button-submit.vue').default);
Vue.component('button-cancel', require('./bootstrap5/buttons/button-cancel.vue').default);
Vue.component('button-reset', require('./bootstrap5/buttons/button-reset.vue').default);

//Card
Vue.component('card', require("./bootstrap5/card/card.vue").default);
Vue.component('card-main', require("./bootstrap5/card/card-main.vue").default);
Vue.component('card-body', require("./bootstrap5/card/card-body.vue").default);
Vue.component('card-columns', require("./bootstrap5/card/card-columns.vue").default);
Vue.component('card-deck', require("./bootstrap5/card/card-deck.vue").default);
Vue.component('card-footer', require("./bootstrap5/card/card-footer.vue").default);
Vue.component('card-bottom', require("./bootstrap5/card/card-bottom.vue").default);
Vue.component('card-group', require("./bootstrap5/card/card-group.vue").default);
Vue.component('card-header', require("./bootstrap5/card/card-header.vue").default);
Vue.component('card-img-bottom', require("./bootstrap5/card/card-img-bottom.vue").default);
Vue.component('card-img-top', require("./bootstrap5/card/card-img-top.vue").default);
Vue.component('card-main-header', require("./bootstrap5/card/card-main-header.vue").default);
Vue.component('card-text', require("./bootstrap5/card/card-text.vue").default);
Vue.component('card-title', require("./bootstrap5/card/card-title.vue").default);
Vue.component('card', require("./bootstrap5/card/card.vue").default);
Vue.component('card-main', require("./bootstrap5/card/card-main.vue").default);

//Dialoge
Vue.component('dialog-delete', require('./bootstrap5/dialog/delete.vue').default);

//Editor
Vue.component('html-editor', require('./bootstrap5/editor/sceditor.vue').default);
//Vue.component('html-editor', require('./bootstrap5/editor/tinymce.vue').default);

//Forms
Vue.component('checkbox', require('./bootstrap5/forms/checkbox.vue').default);
Vue.component('combobox', require('./bootstrap5/forms/combobox.vue').default);
//Vue.component('editor', require('./bootstrap5/editor.vue').default);
Vue.component('hform', require('./bootstrap5/forms/hform.vue').default);
Vue.component('hlabel', require('./bootstrap5/forms/hlabel.vue').default);
Vue.component('input-color', require('./bootstrap5/forms/input-color.vue').default);
Vue.component('input-date', require('./bootstrap5/forms/input-date.vue').default);
Vue.component('input-email', require('./bootstrap5/forms/input-email.vue').default);
Vue.component('input-euro', require('./bootstrap5/forms/input-euro.vue').default);
Vue.component('input-file-img', require('./bootstrap5/forms/input-file-img.vue').default);
Vue.component('input-file', require('./bootstrap5/forms/input-file.vue').default);
Vue.component('input-group-text', require('./bootstrap5/forms/input-group-text.vue').default);
Vue.component('input-group', require('./bootstrap5/forms/input-group.vue').default);
Vue.component('input-hidden', require('./bootstrap5/forms/input-hidden.vue').default);
Vue.component('input-int', require('./bootstrap5/forms/input-int.vue').default);
Vue.component('input-number', require('./bootstrap5/forms/input-number.vue').default);
Vue.component('input-password', require('./bootstrap5/forms/input-password.vue').default);
Vue.component('input-percent', require('./bootstrap5/forms/input-percent.vue').default);
Vue.component('input-text', require('./bootstrap5/forms/input-text.vue').default);
Vue.component('radiobox', require('./bootstrap5/forms/radiobox.vue').default);
Vue.component('search', require('./bootstrap5/forms/search.vue').default);
Vue.component('text-area', require('./bootstrap5/forms/text-area.vue').default);
Vue.component('vue-select', require('./bootstrap5/forms/vue-select.vue').default);
Vue.component('vue-select-item', require('./bootstrap5/forms/vue-select-item.vue').default);


//Grid
Vue.component('row', require('./bootstrap5/grid/row.vue').default);
Vue.component('col-1', require('./bootstrap5/grid/col-1.vue').default);
Vue.component('col-2', require('./bootstrap5/grid/col-2.vue').default);
Vue.component('col-3', require('./bootstrap5/grid/col-3.vue').default);
Vue.component('col-4', require('./bootstrap5/grid/col-4.vue').default);
Vue.component('col-5', require('./bootstrap5/grid/col-5.vue').default);
Vue.component('col-6', require('./bootstrap5/grid/col-6.vue').default);
Vue.component('col-7', require('./bootstrap5/grid/col-7.vue').default);
Vue.component('col-8', require('./bootstrap5/grid/col-8.vue').default);
Vue.component('col-9', require('./bootstrap5/grid/col-9.vue').default);
Vue.component('col-10', require('./bootstrap5/grid/col-10.vue').default);
Vue.component('col-11', require('./bootstrap5/grid/col-11.vue').default);
Vue.component('col-12', require('./bootstrap5/grid/col-12.vue').default);

//Modal
Vue.component('modal', require('./bootstrap5/modal/modal.vue').default);
Vue.component('modal-header', require('./bootstrap5/modal/modal-header.vue').default);
Vue.component('modal-body', require('./bootstrap5/modal/modal-body.vue').default);
Vue.component('modal-footer', require('./bootstrap5/modal/modal-footer.vue').default);
Vue.component('modal-button-close', require('./bootstrap5/modal/modal-button-close.vue').default);
Vue.component('modal-open-button', require('./bootstrap5/modal/modal-open-button.vue').default);


//Nav
Vue.component('nav-group', require('./bootstrap5/nav/nav-group.vue').default);
Vue.component('nav-item', require('./bootstrap5/nav/nav-item.vue').default);


//Show
Vue.component('txt', require('./bootstrap5/show/show-text.vue').default);
Vue.component('euro', require('./bootstrap5/show/show-euro.vue').default);
Vue.component('datum', require('./bootstrap5/show/show-date.vue').default);
Vue.component('code-show', require('./bootstrap5/show/code.vue').default);

//Tabs
Vue.component('tabs-header', require('./bootstrap5/tabs/tabs-header.vue').default);
Vue.component('tab-header', require('./bootstrap5/tabs/tab-header.vue').default);
Vue.component('tabs-body', require('./bootstrap5/tabs/tabs-body.vue').default);
Vue.component('tab-body', require('./bootstrap5/tabs/tab-body.vue').default);

//Sidebar
Vue.component('sidebar', require('./bootstrap5/sidebar/sidebar.vue').default);
Vue.component('sidebar-group', require('./bootstrap5/sidebar/sidebar-group.vue').default);
Vue.component('sidebar-item', require('./bootstrap5/sidebar/sidebar-item.vue').default);
Vue.component('sidebar-header', require('./bootstrap5/sidebar/sidebar-header.vue').default);
Vue.component('sidebar-line', require('./bootstrap5/sidebar/sidebar-line.vue').default);
Vue.component('sidebar-parent-item', require('./bootstrap5/sidebar/sidebar-parent-item.vue').default);
Vue.component('sidebar-child-item', require('./bootstrap5/sidebar/sidebar-child-item.vue').default);

//SVG
Vue.component('svg-pen', require('./bootstrap5/svg/pen.vue').default);
Vue.component('svg-star', require('./bootstrap5/svg/star.vue').default);

//View
Vue.component('rating', require('./bootstrap5/view/rating.vue').default);


//Design
Vue.component('group', require('./bootstrap5/group.vue').default);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#vue-app',
});