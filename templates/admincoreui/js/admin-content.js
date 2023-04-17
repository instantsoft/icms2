function contentCatsReorder(button){
    icms.modal.openAjax(button.attr('href'),{},false,$(button).attr('title'));
    return false;
}

function contentItemsMoved(form_data){
    icms.datagrid.loadRows();
    icms.modal.close();
}

function contentItemsEditSelected(form_data, result){
    $('#icms_modal .modal-body').html(result.html);
}
function contentItemsEditSelectedSaved(form_data, result){
    window.location.href = result.location;
}
