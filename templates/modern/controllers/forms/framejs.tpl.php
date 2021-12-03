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

    let data = JSON.parse(event.data);

    document.getElementById(data.id).style.height = data.height+'px';
}

document.writeln('<iframe id="embed-form-<?php echo $form_data['hash']; ?>" width="100%" src="<?php echo href_to_abs('forms', 'embed', $form_data['hash']); ?>" height="0" border="0" framespacing="0" frameborder="0"></iframe>');