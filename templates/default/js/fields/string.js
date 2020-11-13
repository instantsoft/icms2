$(function(){
    var tail = document.location.href.split('/').reverse();
    $('#in_filter_as').on('change', function(){
        var hint = $(this).parents('.field:first').find('.hint');
        if($(this).val() !== 'select' && (parseInt(tail[1]) > 0)){
            hint.hide();
        }else{
            hint.show();
        }
    }).trigger('change');
});
function fieldStringLoadDefault(url){
    var tail = document.location.href.split('/').reverse();
    if(parseInt(tail[1]) > 0){ /* это не добавление нового */
        $.post(url+'/'+tail[1]+'/'+tail[0], {}, function(data){
            if(data.error === false){
                $('#values').val(data.result);
                $('html, body').animate({
                    scrollTop: $('#values').offset().top - 75
                }, 250);
            }
        }, 'json');
    }
    return false;
}