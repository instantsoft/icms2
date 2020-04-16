var is_filter = false;

function contentFilter(){
    var form_data = $('.datagrid_dataset_filter form').serialize();
    $('#datagrid_filter #advanced_filter').val(form_data);
    $('.cp_toolbar .filter a').hide();
    $('.cp_toolbar .delete_filter a').show();
    icms.datagrid.setPage(1);
    icms.datagrid.loadRows(); // В UPS diff_order обновлется только здесь
    icms.modal.close();
    is_filter = true;
    return false;
}

function contentGridColumnsSettings(){
    var form_data = icms.forms.toJSON($('.datagrid_grid_columns_settings form'));
    form_data.submit = true;
    $.post($('.datagrid_grid_columns_settings form').attr('action'), form_data, function(data){
        if(data.error === false){
            icms.datagrid.setPage(1);
            icms.datagrid.loadRows();
            icms.modal.close();
        }
    }, 'json');
    return false;
}

function contentGridColumnsResetSettings(){
    $.post($('.datagrid_grid_columns_settings form').attr('action'), {reset: true}, function(data){
        if(data.error === false){
            icms.datagrid.setPage(1);
            icms.datagrid.loadRows();
            icms.modal.close();
        }
    }, 'json');
    return false;
}

function contentCancelFilter(ctype_changed){
    $('#datagrid_filter #advanced_filter').val('');
    $('.cp_toolbar .filter a').show();
    $('.cp_toolbar .delete_filter a').hide();
    icms.datagrid.setPage(1);
    $('#datagrid_filter input[name=ctype_changed]').val(ctype_changed ? '1' : '0');
    icms.datagrid.loadRows();
    $('#datagrid_filter input[name=ctype_changed]').val('0');
    is_filter = false;
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
function contentItemsEditSelected(form_data, result){
    $('#popup-manager .nyroModalLink').html(result.html);
}
function contentItemsEditSelectedSaved(form_data, result){
    window.location.href = result.location;
}
