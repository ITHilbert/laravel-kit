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