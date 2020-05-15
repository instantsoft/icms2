if ( window.addEventListener ){
    window.addEventListener("message", receiveFormMessage, false);
}
if ( window.attachEvent ) {
	window.attachEvent("onmessage", receiveFormMessage);
}
if ( document.attachEvent ) {
	document.attachEvent("onmessage", receiveFormMessage);
}
function receiveFormMessage(event){
    $('#icms_modal .embed-responsive').height(event.data);
}
function widgetEdit(link){

    var title = $(link).attr('title');
    var id = $(link).data('id');
    var url = $(link).data('url');
    var name = $(link).data('name');

    icms.modal.openAjax(url + '/' + id, {template:name, is_iframe: true}, false, title);

    return false;

}
function widgetDelete(link){

    var id = $(link).data('id');
    var url = $(link).data('url');
    var confirm_text = $(link).data('confirm');

    if (!confirm(confirm_text)){return false;}

    var widget_dom = $( "#widget_wrapper_" + id);

    $.post(url + '/' + id, {}, function(){
        window.location.reload();
    });

    return false;

}