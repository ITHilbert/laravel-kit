<template>
    <form v-bind:id="id" v-bind:method="method">
        <input type="hidden" name="_token" v-bind:value="csrf" />
        <slot></slot>
    </form>
</template>

<script>
    export default {
        props: {
          'id': {
              default: 'myform'
          },
          'method': {
              default: 'post'
          }
        },
        data: function(){
            return {
                csrf: "",
            }
        },
        mounted() {
            this.csrf = document.querySelector('meta[name="csrf-token"]').content;
            //this.csrf = window.Laravel.csrfToken;
        }
    }
</script>

<style>

</style>
