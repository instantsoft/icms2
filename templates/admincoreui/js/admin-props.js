var hide_filter_props_list = true;
var icms = icms || {};
icms.adminProps = (function ($) {

    this.cookie_path_key = '';
    this.props_bind_url = '';
    this.datagrid_url = '';
    this.tree_ajax_url = '';
    this.back_url = '';

    let self = this;

    this.onDocumentReady = function(){

        let is_loaded = false;

        $("#datatree").dynatree({
            onPostInit: function(isReloading, isError){
                var path = $.cookie(self.cookie_path_key);
                if (!path) {
                    $('a', "#datatree").eq(0).trigger('click');
                }
                if (path) {
                    $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                        if(status === "loaded") {
                            node.expand();
                        }else if(status === "ok") {
                            node.activate();
                            node.expand();
                        }
                    });
                }
            },
            onActivate: function(node){
                node.expand();
                $.cookie(self.cookie_path_key, node.getKeyPath(), {expires: 7, path: '/'});
                var key = node.data.key.split('.');
                $('.cp_toolbar .add').show();
                $('.cp_toolbar .edit_folder, .cp_toolbar .delete_folder').show();
                if(key[1] == 0){
                    hide_filter_props_list = true;
                    $('.cp_toolbar .add, .cp_toolbar .edit_folder, .cp_toolbar .delete_folder').hide();
                } else {
                    hide_filter_props_list = false;
                }

                $('.cp_toolbar .datagrid_change a').each(function (){

                    let href = $(this).data('href');

                    href += '/'+ key[0] + '/' + key[1] + '?back='+self.back_url;

                    if($(this).closest('.datagrid_change').hasClass('datagrid_csrf')){
                        href += '&csrf_token='+icms.forms.getCsrfToken();
                    }

                    $(this).attr('href', href);
                });

                $('form#props-bind').attr('action', self.props_bind_url+"/" + key[0] + "/" + key[1]);
                if (!node.bExpanded){
                    $('#props-bind #is_childs .input-checkbox').removeAttr('checked');
                    $('#props-bind #is_childs').hide();
                } else {
                    $('#props-bind #is_childs .input-checkbox').attr('checked', 'checked');
                    $('#props-bind #is_childs').show();
                }
                $('.breadcrumb-item.active').html(node.data.title);
                if (!is_loaded){
                    is_loaded = true;
                    icms.datagrid.init();
                }
                icms.datagrid.setURL(self.datagrid_url+"/" + key[1]);
                icms.datagrid.loadRows(filterPropsList);
            },
            onLazyRead: function(node){
                node.appendAjax({
                    url: self.tree_ajax_url,
                    data: {
                        id: node.data.key
                    }
                });
            }
        });
    };

    return this;

}).call(icms.adminProps || {},jQuery);

function filterPropsList(){

    if(hide_filter_props_list === true){
        $('#props-bind').hide();
    } else {
        $('#props-bind').show();
    }

    if ($('select[name=props_list]').length === 0) { return; }

    var full_list = $('select[name=props_list]');
    var current_list = $('#ctypes-props-toolbar select[name=prop_id]');

    current_list.html(full_list.html());

    if ($('#datagrid tbody tr').not('.empty_tr').length === 0){ return; }

    $('#datagrid tbody tr').each(function(){

       var row = $(this);
       var prop_id = $('a.edit', row).attr('href').split('/').pop();

       $('option[value='+prop_id+']', current_list).remove();

    });

    if ($('option', current_list).length === 0) { $('#props-bind').hide(); }

}