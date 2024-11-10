$(function(){
    let tail = document.location.href.split('/').reverse();
    $('#options_in_filter_as').on('change', function(){
        let hint = $(this).parents('.field:first').find('.hint');
        let ctype_id = parseInt(tail[1]);
        if(ctype_id === NaN || !ctype_id || $(this).val() !== 'select'){
            hint.hide(); return;
        }
        if($(this).val() === 'select'){
            hint.show();
        }
    }).trigger('change');
    $('#f_options_in_filter_as .string-display-variant').on('click', function(){
        let url = $(this).data('field_string_ajax_url');
        $.post(url+'/'+tail[1]+'/'+tail[0], {}, function(data){
            if(data.error === false){
                $('#values').val(data.result);
                $('html, body').animate({
                    scrollTop: $('#values').offset().top - 75
                }, 250);
            }
        }, 'json');
        return false;
    });
});