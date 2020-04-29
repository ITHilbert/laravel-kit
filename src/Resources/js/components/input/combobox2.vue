<template>
 <div class="col-md-6">
	<div class="input-group mb-3">
	  <input type="text" placeholder="search user here...." v-model="searchquery" v-on:click="seen = !seen"   class="form-control">
	 <div class="input-group-append" >
		<span class="input-group-text" id="basic-addon2" v-on:click="seen = !seen ; "><i class="fas fa-chevron-down"></i></span>
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
