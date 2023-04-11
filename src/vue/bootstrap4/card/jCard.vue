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
              default: 'bg-light mb-3'
          },
          'classheader': {
              default: 'font-weight-bold'
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
                'defaultclasscard': 'card ',
                'defaultclassheader': 'card-header ',
                'defaultclassbody': 'card-body ',
                'defaultclassfooter': 'card-footer ',
            }
        },
    }
</script>

<style>
    
</style>
