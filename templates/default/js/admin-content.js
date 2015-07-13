function contentFilter(){
    var form_data = $('.datagrid_dataset_filter form').serialize();
    $('#datagrid_filter #advanced_filter').val(form_data);
    $('.cp_toolbar .filter a').hide();
    $('.cp_toolbar .delete_filter a').show();
    icms.datagrid.setPage(1);
    icms.datagrid.loadRows();
    icms.modal.close();
    return false;
}

function contentCancelFilter(){
    $('#datagrid_filter #advanced_filter').val('');
    $('.cp_toolbar .filter a').show();
    $('.cp_toolbar .delete_filter a').hide();
    icms.datagrid.setPage(1);
    icms.datagrid.loadRows();
    return false;
}

function contentCatsReorder(button){
    var url = button.attr('href');
    icms.modal.openAjax(url);
    return false;
}

function contentItemsMoved(form_data){
    icms.datagrid.loadRows();
    icms.modal.close();
}
