$(document).ready(function() {

    //######################################################################
    //Formats numbers from DE format to US format before the form is sent

    $('#myform').submit(function() {
        $('.input-euro').each(function() {
            str = $(this).val();

            str = str.replace('.', '');
            str = str.replace(' ', '');
            str = str.replace(',', '.');


            $(this).val(str);
        });


        $('.input-percent').each(function() {
            str = $(this).val();

            str = str.replace('.', '');
            str = str.replace(' ', '');
            str = str.replace(',', '.');


            $(this).val(str);
        });


        $('.input-int').each(function() {
            str = $(this).val();

            str = str.replace('.', '');
            str = str.replace(' ', '');
            str = str.replace(',', '.');


            $(this).val(str);
        });

        $('.input-number').each(function() {
            str = $(this).val();

            str = str.replace('.', '');
            str = str.replace(' ', '');
            str = str.replace(',', '.');


            $(this).val(str);
        });


        return true; // return false to cancel form action
    });


    //######################################################################

    /* $('body').on('click', '.delete-customer', function(e){
    
			console.log(1);
			var $form=$(this).next('form');
			$('#confirm').modal({ backdrop: 'static', keyboard: false })
				.on('click', '#delete-btn', function(){
					$form.submit();
				});
		});
     */
    /****on select project****/
    /* $('body').on('change', '#project_id', function(e){
    
			var value = $(this).val();
			if(value)
			{
				var res = value.split("|");
				$('#hourly_rate').val(res[1]);
				$('#mileage_allowance').val(res[2]);
			} 
			 
		});
                 */






    //#############################################################

}); //Ende On Load ready


//Nötig für das löschen von Datensätzen
var deleteID;

function OpenDeleteForm(id) {
    deleteID = id;
    $('#myModalDelete').modal('show');
}