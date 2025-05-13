if ( window.addEventListener ){
    window.addEventListener("message", receiveFormMessage, false);
}
if ( window.attachEvent ) {
    window.attachEvent("onmessage", receiveFormMessage);
}
if ( document.attachEvent ) {
    document.attachEvent("onmessage", receiveFormMessage);
}
$(function(){
    $('.edit_wlinks .edit').on('click', function (){
        return widgetEdit(this);
    });
    $('.edit_wlinks .delete').on('click', function (){
        return widgetDelete(this);
    });
});
function receiveFormMessage(event){
    $('#icms_modal .embed-responsive').height(event.data);
}
function widgetEdit(link){

    let title = $(link).attr('title');
    let id = $(link).data('id');
    let url = $(link).data('url');
    let name = $(link).data('name');

    icms.modal.openAjax(url + '/' + id, {template:name, is_iframe: true}, false, title);

    return false;
}
function widgetDelete(link){

    let id = $(link).data('id');
    let url = $(link).data('url');
    let confirm_text = $(link).data('confirm');

    if (!confirm(confirm_text)){return false;}

    $.post(url + '/' + id, {}, function(){
        window.location.reload();
    });

    return false;
}