 <!-- https://www.npmjs.com/package/@tinymce/tinymce-vue -->
 <template>
     <teditor
       api-key="no-api-key"
       :init="{
         height: 500,
         menubar: false,
         plugins: [
           'advlist autolink lists link image charmap print preview anchor',
           'searchreplace visualblocks code fullscreen',
           'insertdatetime media table paste code help wordcount'
         ],
         toolbar:
           'undo redo | formatselect | bold italic backcolor | \
           alignleft aligncenter alignright alignjustify | \
           bullist numlist outdent indent | removeformat | help'
       }"
     ></teditor>
 </template>

 <script>
 import Editor from '@tinymce/tinymce-vue'

 export default {
   components: {
     'teditor': Editor
   }
 }
 </script>
