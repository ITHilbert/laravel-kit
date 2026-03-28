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
 * (Moved to bootstrap.js)
 */

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./tailwind/', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


//Accordion
Vue.component('accordion', require('./tailwind/accordion/accordion.vue').default);
Vue.component('accordion-body', require('./tailwind/accordion/accordion-body.vue').default);
Vue.component('accordion-header', require('./tailwind/accordion/accordion-header.vue').default);
Vue.component('accordion-item', require('./tailwind/accordion/accordion-item.vue').default);

//Breadcrumb
Vue.component('breadcrumb', require('./tailwind/breadcrumb/breadcrumb.vue').default);
Vue.component('breadcrumb-item', require('./tailwind/breadcrumb/breadcrumb-item.vue').default);


//Buttons
Vue.component('hbutton', require('./tailwind/buttons/hbutton.vue').default);
Vue.component('button-create', require("./tailwind/buttons/button-create.vue").default);
Vue.component('button-show', require('./tailwind/buttons/button-show.vue').default);
Vue.component('button-edit', require('./tailwind/buttons/button-edit.vue').default);
Vue.component('button-delete', require('./tailwind/buttons/button-delete.vue').default);
Vue.component('button-save', require('./tailwind/buttons/button-save.vue').default);
Vue.component('button-back', require('./tailwind/buttons/button-back.vue').default);
Vue.component('button-submit', require('./tailwind/buttons/button-submit.vue').default);
Vue.component('button-cancel', require('./tailwind/buttons/button-cancel.vue').default);
Vue.component('button-reset', require('./tailwind/buttons/button-reset.vue').default);

//Card
Vue.component('card', require("./tailwind/card/card.vue").default);
Vue.component('card-main', require("./tailwind/card/card-main.vue").default);
Vue.component('card-body', require("./tailwind/card/card-body.vue").default);
Vue.component('card-columns', require("./tailwind/card/card-columns.vue").default);
Vue.component('card-deck', require("./tailwind/card/card-deck.vue").default);
Vue.component('card-footer', require("./tailwind/card/card-footer.vue").default);
Vue.component('card-bottom', require("./tailwind/card/card-bottom.vue").default);
Vue.component('card-group', require("./tailwind/card/card-group.vue").default);
Vue.component('card-header', require("./tailwind/card/card-header.vue").default);
Vue.component('card-img-bottom', require("./tailwind/card/card-img-bottom.vue").default);
Vue.component('card-img-top', require("./tailwind/card/card-img-top.vue").default);
Vue.component('card-main-header', require("./tailwind/card/card-main-header.vue").default);
Vue.component('card-text', require("./tailwind/card/card-text.vue").default);
Vue.component('card-title', require("./tailwind/card/card-title.vue").default);
Vue.component('card', require("./tailwind/card/card.vue").default);
Vue.component('card-main', require("./tailwind/card/card-main.vue").default);

//Dialoge
Vue.component('dialog-delete', require('./tailwind/dialog/delete.vue').default);

//Editor
Vue.component('html-editor', require('./tailwind/editor/sceditor.vue').default);
//Vue.component('html-editor', require('./tailwind/editor/tinymce.vue').default);

//Forms
Vue.component('checkbox', require('./tailwind/forms/checkbox.vue').default);
Vue.component('combobox', require('./tailwind/forms/combobox.vue').default);
//Vue.component('editor', require('./tailwind/editor.vue').default);
Vue.component('hform', require('./tailwind/forms/hform.vue').default);
Vue.component('hlabel', require('./tailwind/forms/hlabel.vue').default);
Vue.component('input-color', require('./tailwind/forms/input-color.vue').default);
Vue.component('input-date', require('./tailwind/forms/input-date.vue').default);
Vue.component('input-email', require('./tailwind/forms/input-email.vue').default);
Vue.component('input-euro', require('./tailwind/forms/input-euro.vue').default);
Vue.component('input-file-img', require('./tailwind/forms/input-file-img.vue').default);
Vue.component('input-file', require('./tailwind/forms/input-file.vue').default);
Vue.component('input-group-text', require('./tailwind/forms/input-group-text.vue').default);
Vue.component('input-group', require('./tailwind/forms/input-group.vue').default);
Vue.component('input-hidden', require('./tailwind/forms/input-hidden.vue').default);
Vue.component('input-int', require('./tailwind/forms/input-int.vue').default);
Vue.component('input-number', require('./tailwind/forms/input-number.vue').default);
Vue.component('input-password', require('./tailwind/forms/input-password.vue').default);
Vue.component('input-percent', require('./tailwind/forms/input-percent.vue').default);
Vue.component('input-text', require('./tailwind/forms/input-text.vue').default);
Vue.component('radiobox', require('./tailwind/forms/radiobox.vue').default);
Vue.component('search', require('./tailwind/forms/search.vue').default);
Vue.component('text-area', require('./tailwind/forms/text-area.vue').default);
Vue.component('vue-select', require('./tailwind/forms/vue-select.vue').default);
Vue.component('vue-select-item', require('./tailwind/forms/vue-select-item.vue').default);


//Grid
Vue.component('row', require('./tailwind/grid/row.vue').default);
Vue.component('col-1', require('./tailwind/grid/col-1.vue').default);
Vue.component('col-2', require('./tailwind/grid/col-2.vue').default);
Vue.component('col-3', require('./tailwind/grid/col-3.vue').default);
Vue.component('col-4', require('./tailwind/grid/col-4.vue').default);
Vue.component('col-5', require('./tailwind/grid/col-5.vue').default);
Vue.component('col-6', require('./tailwind/grid/col-6.vue').default);
Vue.component('col-7', require('./tailwind/grid/col-7.vue').default);
Vue.component('col-8', require('./tailwind/grid/col-8.vue').default);
Vue.component('col-9', require('./tailwind/grid/col-9.vue').default);
Vue.component('col-10', require('./tailwind/grid/col-10.vue').default);
Vue.component('col-11', require('./tailwind/grid/col-11.vue').default);
Vue.component('col-12', require('./tailwind/grid/col-12.vue').default);

//Modal
Vue.component('modal', require('./tailwind/modal/modal.vue').default);
Vue.component('modal-header', require('./tailwind/modal/modal-header.vue').default);
Vue.component('modal-body', require('./tailwind/modal/modal-body.vue').default);
Vue.component('modal-footer', require('./tailwind/modal/modal-footer.vue').default);
Vue.component('modal-button-close', require('./tailwind/modal/modal-button-close.vue').default);
Vue.component('modal-open-button', require('./tailwind/modal/modal-open-button.vue').default);


//Nav
Vue.component('nav-group', require('./tailwind/nav/nav-group.vue').default);
Vue.component('nav-item', require('./tailwind/nav/nav-item.vue').default);


//Show
Vue.component('txt', require('./tailwind/show/show-text.vue').default);
Vue.component('euro', require('./tailwind/show/show-euro.vue').default);
Vue.component('datum', require('./tailwind/show/show-date.vue').default);
Vue.component('code-show', require('./tailwind/show/code.vue').default);

//Tabs
Vue.component('tabs-header', require('./tailwind/tabs/tabs-header.vue').default);
Vue.component('tab-header', require('./tailwind/tabs/tab-header.vue').default);
Vue.component('tabs-body', require('./tailwind/tabs/tabs-body.vue').default);
Vue.component('tab-body', require('./tailwind/tabs/tab-body.vue').default);

//Sidebar
Vue.component('sidebar', require('./tailwind/sidebar/sidebar.vue').default);
Vue.component('sidebar-group', require('./tailwind/sidebar/sidebar-group.vue').default);
Vue.component('sidebar-item', require('./tailwind/sidebar/sidebar-item.vue').default);
Vue.component('sidebar-header', require('./tailwind/sidebar/sidebar-header.vue').default);
Vue.component('sidebar-line', require('./tailwind/sidebar/sidebar-line.vue').default);
Vue.component('sidebar-parent-item', require('./tailwind/sidebar/sidebar-parent-item.vue').default);
Vue.component('sidebar-child-item', require('./tailwind/sidebar/sidebar-child-item.vue').default);

//SVG
Vue.component('svg-pen', require('./tailwind/svg/pen.vue').default);
Vue.component('svg-star', require('./tailwind/svg/star.vue').default);

//View
Vue.component('rating', require('./tailwind/view/rating.vue').default);


//Design
Vue.component('group', require('./tailwind/group.vue').default);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#vue-app',
});