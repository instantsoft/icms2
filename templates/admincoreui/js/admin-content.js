var icms = icms || {};
icms.events.on('datagrid_mounted', function(gridApp){
    let datatree = $("#datatree");
    let content_url = datatree.data('content_url');
    let current_ctype_id = datatree.data('ctype_id');
    let ctype_edit = datatree.data('ctype_edit');
    let moderation_url = datatree.data('moderation_url');
    let cp_toolbar = $('.cp_toolbar');
    let is_init = false;
    $("#datatree").dynatree({
        debugLevel: 0,
        onPostInit: function(isReloading, isError){
            let path = ''+datatree.data('key_path');
            this.loadKeyPath(path, function(node, status){
                if(status === "loaded") {
                    node.expand();
                } else if(status === "ok") {
                    node.activate();
                    node.expand();
                }
            });
        },
        onActivate: function(node){
            node.expand();
            $.cookie('icms[content_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
            let key = node.data.key.split('.');
            icms.datagrid.setURL(content_url+'/' + key[0] + '/' + key[1]);
            if (is_init) {
                if(key[0] !== current_ctype_id){
                    current_ctype_id = key[0];
                    gridApp.filter = {};
                }
                icms.datagrid.loadRows();
            }
            is_init = true;
            gridApp.select_actions_items_map = key;
            $('.settings a', cp_toolbar).attr('href', ctype_edit+'/' + key[0]);
            $('.logs a', cp_toolbar).attr('href', moderation_url+'/' + key[0]);
            $('.add_folder a', cp_toolbar).attr('href', content_url+'/cats_add/' + key[0] + '/' + key[1]);
            $('.edit_folder a', cp_toolbar).attr('href', content_url+'/cats_edit/' + key[0] + '/' + key[1]);
            $('.delete_folder a', cp_toolbar).attr('href', content_url+'/cats_delete/' + key[0] + '/' + key[1] + '?csrf_token='+icms.forms.getCsrfToken());
            $('.tree_folder a', cp_toolbar).attr('href', content_url+'/cats_order/' + key[0]);
            $('.add.add_site a', cp_toolbar).attr('href', content_url+'/item_add/' + key[0] + '/' + key[1]);
            $('.add.add_cpanel a', cp_toolbar).attr('href', content_url+'/item_add/' + key[0] + '/' + key[1] + '/1');

            if (key[1] === '1'){
                $('.edit_folder a', cp_toolbar).hide();
                $('.delete_folder a', cp_toolbar).hide();
            } else {
                $('.folder', cp_toolbar).addClass('animated animate-shake');
                $('.edit_folder a', cp_toolbar).show();
                $('.delete_folder a', cp_toolbar).show();
            }
            var root_node = null;
            node.visitParents(function (_node) {
                if(_node.parent !== null){
                    root_node = _node;
                }
            }, true);
            $('.breadcrumb-item.active').html(root_node.data.title);
            window.history.pushState(null, null, content_url+'/'+key[0]);
        },
        onLazyRead: function(node){
            node.appendAjax({
                url: content_url+'/tree_ajax',
                data: {
                    id: node.data.key
                }
            });
        }
    });
});

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
