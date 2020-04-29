<template>
    <v-select label="first_name" :options="users"  v-model="select" :value="select" @input="setSelected"></v-select>
</template>

<script>
/*****v-select components
 https://vue-select.org/ *****/
import vSelect from 'vue-select';
import "vue-select/src/scss/vue-select.scss";

export default {
    props: ["users"],	
  components: {
    'v-select': vSelect
  },
  
  data() {
    return {
      select: null,
      
      
    };
  } , 
  methods: {
	  setSelected(value) {
		 //  trigger a mutation, or dispatch an action  
		  
		 $('#select_user_id').val(value.id);
		 /****get projects based on selected customer***/
		    axios.post('/customer/projects', {'customer_id':value.id}).then(function (response) {
				var select = document.getElementById("project_id");
				select.innerHTML = "";
				var json=response.data;
				var options='';
				 for(var i = 0; i < json.length; i++) {
						var obj = json[i]; 
						 /**add empty option**/
						 
						var option = document.createElement("option");
						option.text = obj.project;
						option.value = obj.id+'|'+obj.hourly_rate+'|'+obj.mileage_allowance;
						
						select.appendChild(option);
						$('#project_id').trigger('change');
						 
					}
					 
                }).catch(error => {
					 
			  });
		  
	  }
	}
}
</script>
