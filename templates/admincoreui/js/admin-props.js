var icms = icms || {};
icms.adminProps = (function ($) {

    this.cookie_path_key = '';
    this.tree_path_key = '';
    this.props_bind_url = '';
    this.datagrid_url = '';
    this.tree_ajax_url = '';
    this.back_url = '';
    this.hide_filter_props_list = true;
    this.props_bind = {};

    let self = this;

    this.init = function(gridApp){

        this.props_bind = $('#props-bind');

        this.props_bind.on('submit', function (){
            return icms.forms.submitAjax(this);
        });

        let is_init = false;

        $("#datatree").dynatree({
            onPostInit: function(isReloading, isError){
                this.loadKeyPath(self.tree_path_key, function(node, status){
                    if(status === 'loaded') {
                        node.expand();
                    } else if(status === 'ok') {
                        node.activate();
                        node.expand();
                    }
                });
            },
            onActivate: function(node){
                let key_path = node.data.key.split('.');
                let cat_id = +key_path[1];
                node.expand();
                $.cookie(self.cookie_path_key, node.getKeyPath(), {expires: 7, path: '/'});
                let hidden_menu = $('.cp_toolbar .add, .cp_toolbar .edit_folder, .cp_toolbar .delete_folder');
                if(cat_id === 0){
                    self.hide_filter_props_list = true;
                    hidden_menu.addClass('d-none');
                } else {
                    self.hide_filter_props_list = false;
                    hidden_menu.removeClass('d-none');
                }

                if (is_init) {
                    icms.datagrid.setURL(self.datagrid_url +'/' + cat_id);
                    icms.datagrid.loadRows();
                } else {
                    self.filterPropsList();
                }
                is_init = true;

                $('.cp_toolbar .datagrid_change a').each(function (){

                    let href = $(this).data('href');

                    href += '/'+ cat_id + '?back='+self.back_url;

                    if($(this).closest('.datagrid_change').hasClass('datagrid_csrf')){
                        href += '&csrf_token='+icms.forms.getCsrfToken();
                    }

                    $(this).attr('href', href);
                });

                self.props_bind.attr('action', self.props_bind_url+"/" + cat_id);
                if (!node.bExpanded){
                    $('#is_childs .input-checkbox', self.props_bind).removeAttr('checked');
                    $('#is_childs', self.props_bind).hide();
                } else {
                    $('#is_childs .input-checkbox', self.props_bind).attr('checked', 'checked');
                    $('#is_childs', self.props_bind).show();
                }
                $('.breadcrumb-item.active').html(node.data.title);
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

        icms.events.on('datagrid_updated', function(gridApp){
            self.filterPropsList();
        });
    };

    this.propBinded = function (form_data, result) {
        toastr.success(result.success_text);
        icms.datagrid.loadRows();
    };

    this.filterPropsList = function () {

        if(self.hide_filter_props_list){
            self.props_bind.hide();
        } else {
            self.props_bind.show();
        }

        let full_list = $('select[name=props_list]');

        if (full_list.length === 0) { return; }

        let current_list = $('#ctypes-props-toolbar select[name=prop_id]');

        current_list.html(full_list.html());

        if ($('#icms-grid tbody tr').not('.empty_tr').length === 0){ return; }

        $('#icms-grid tbody tr').each(function(){

           let prop_id = $('a.edit', $(this)).attr('href').split('/').pop();
           $('option[value='+prop_id+']', current_list).remove();

        });

        if ($('option', current_list).length === 0) { self.props_bind.hide(); }
    };

    return this;

}).call(icms.adminProps || {},jQuery);