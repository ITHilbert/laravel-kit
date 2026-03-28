<template>
 <div class="md:w-1/2 px-6">
	<div class="flex rounded-md shadow-sm w-full mb-6">
	  <input type="text" placeholder="search user here...." v-model="searchquery" v-on:click="seen = !seen"   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500">
	 <div class="input-group-append" >
		<span class="inline-flex items-center px-6 border border-gray-200 dark:border-gray-700 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" id="basic-addon2" v-on:click="seen = !seen ; "><i class="fas fa-chevron-down"></i></span>
	  </div>
	  <ul class="slectbox" v-if="seen" >
		<li v-for="result in filteredItems" v-on:click="onChange(result.name)" ><div class="ui-menu-item-wrapper">{{result.name}}</div></li>
	  </ul>
	 </div>
 </div>

</template>
<script>
export default{
    props:['data'],
    name: 'autocomplete',
     
    data(){
        return {
            searchquery: '', 
            seen: false,

        }
    },
    computed:{
        filteredItems: function () {
            var query = this.searchquery;

            return this.data.filter(function (item) {
                return item.name.toLowerCase().indexOf(query.toLowerCase()) !== -1})

        }
     },
     methods: {
        onChange(name) {
            this.seen = false,
            this.searchquery=name;
        },

    },
}
</script>
