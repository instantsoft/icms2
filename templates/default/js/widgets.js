function widgetEdit(id, edit_url){

    icms.modal.openAjax(edit_url + '/' + id, undefined, function (){
        icms.modal.setCallback('close', function(){
            icms.forms.form_changed = false;
        });
        var h = 0, m = false;
        $('.modal_form .form-tabs .tab').each(function(indx, element){
            var th = +$(this).height();
            if (th > h){ h = th; m = true; }
        });
        if(m){
            $('.modal_form .form-tabs .tab').first().css({height: h+'px'});
            setTimeout(function(){ icms.modal.resize(); }, 10);
        }
    });

    return false;

}
function widgetUpdated(widget, result){
    location.reload();
}
function widgetDelete(id, delete_url, LANG_CP_WIDGET_DELETE_CONFIRM){

    if (!confirm(LANG_CP_WIDGET_DELETE_CONFIRM)){return false;}

    var widget_dom = $( "#widget_wrapper_" + id);

    delete_url = delete_url + '/' + id;

    $.post(delete_url, {}, function(){
        widget_dom.fadeOut(300, function(){
            widget_dom.remove();
            if ($('#body aside').text().trim() === ''){
                $('#body aside').remove();
                $('#body section').width('100%');
            }
        });
    });

    return false;

}