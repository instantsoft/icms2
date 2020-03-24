function widgetEdit(id, edit_url, template){

    icms.modal.openAjax(edit_url + '/' + id, {template:template, is_iframe: true});

    return false;

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