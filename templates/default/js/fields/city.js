$(function(){
    $('#location_group').on('input', function (){
        v = $(this).val();
        if(v){
            $('#output_string').parent().show();
        } else {
            $('#output_string').parent().hide();
        }
    }).trigger('input');
});