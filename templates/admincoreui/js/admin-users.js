var icms = icms || {};
icms.events.on('datagrid_mounted', function(app){
    let datatree = $("#datatree");
    let base_url = datatree.data('base_url');
    let is_init = false;
    datatree.dynatree({
        onPostInit: function(){
            let path = $.cookie('icms[users_tree_path]');
            if (!path) { path = '/0'; }
            this.loadKeyPath(path, function(node, status){
                if(status === 'loaded') {
                    node.expand();
                } else if(status === 'ok') {
                    node.activate();
                    node.expand();
                }
            });
        },
        onActivate: function(node){
            if (is_init) {
                icms.datagrid.setURL(base_url+'/' + node.data.key);
                icms.datagrid.loadRows();
            }
            is_init = true;
            node.expand();
            $.cookie('icms[users_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
            let key = +node.data.key;
            $('.cp_toolbar .users_add a').attr('href', base_url+'/add/' + key);
            if (key === 0){
                $('.cp_toolbar .edit_user a').hide();
                $('.cp_toolbar .group_perms').addClass('d-none');
                $('.cp_toolbar .delete_user a').hide();
            } else {
                $('.cp_toolbar .users_group').addClass('animate-shake');
                $('.cp_toolbar .edit_user a').show().attr('href', base_url+'/group_edit/' + key);
                $('.cp_toolbar .group_perms').removeClass('d-none').find('a').attr('href', base_url+'/group_perms/' + key);
                $('.cp_toolbar .delete_user a').show().attr('href', base_url+'/group_delete/' + key + '?csrf_token='+icms.forms.getCsrfToken());
            }
        },
        dnd: {
            onDragStart: function(node) {
                return node.data.key == 0 ? false : true;
            },
            autoExpandMS: 1000,
            preventVoidMoves: true,
            onDragEnter: function(node, sourceNode) {
                if(node.parent !== sourceNode.parent){
                    return false;
                }
                return ["before", "after"];
            },
            onDragOver: function(node, sourceNode, hitMode) {
                if(node.isDescendantOf(sourceNode)){ return false; }
                if(hitMode === "over" ){ return "after"; }
            },
            onDrop: function(node, sourceNode, hitMode, ui, draggable) {
                sourceNode.move(node, hitMode);
                node.expand(true);
                var dict = datatree.dynatree('getTree').toDict();
                $.post(base_url+'/group_reorder', {items: dict}, function(result){
                    toastr.success(result.success_text);
                }, 'json');
            },
            onDragLeave: function(node, sourceNode) {}
        }
    });
});