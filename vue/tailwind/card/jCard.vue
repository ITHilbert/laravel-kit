<template>
    <div v-bind:class="defaultclasscard + classcard">
        <div v-bind:class="defaultclassheader + classheader"><slot name="header">{{ title }}</slot></div>
        <div v-bind:class="defaultclassbody + classbody">
            <slot></slot>
        </div>
        <div v-if="footervisible" v-bind:class="defaultclassfooter + classfooter"><slot name="footer"></slot></div>
    </div>
</template>

<script>
    export default {
        props: {
          'title': {},
          'footervisible': {
              default: false
          },
          'classcard': {
              default: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white'
          },
          'classheader': {
              default: 'font-bold'
          },
          'classbody': {
              default: ''
          },
          'classfooter': {
              default: ''
          }
        },
        data: function(){
            return {
                'defaultclasscard': 'bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 flex flex-col main-card ',
                'defaultclassheader': 'px-6 py-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-t-lg font-bold text-gray-900 dark:text-white ',
                'defaultclassbody': 'p-6 md:p-5 flex-auto ',
                'defaultclassfooter': 'px-6 py-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg ',
            }
        },
    }
</script>

<style>

</style>
