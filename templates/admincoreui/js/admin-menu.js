var icms = icms || {};

icms.events.on('datagrid_mounted', function(app) {
    let datatree = $("#datatree");
    let base_url = datatree.data('base_url');
    let is_init = false;
    datatree.dynatree({
        onPostInit: function(isReloading, isError){
            let path = $.cookie('icms[menu_tree_path]');
            if (!path) { path = '/1.0'; }
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
            $.cookie('icms[menu_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
            let key = node.data.key.split('.');
            $('.cp_toolbar .add_item a').attr('href', base_url+'/item_add/' + key[0] + "/" + key[1]);
            $('.cp_toolbar .edit_menu a').attr('href', base_url+'/edit/' + key[0]);
            $('.cp_toolbar .delete_menu a').attr('href', base_url+'/delete/' + key[0] + '?csrf_token='+icms.forms.getCsrfToken());
            $('.breadcrumb-item.active').html(node.data.title);
        },
        onLazyRead: function(node){
            node.appendAjax({
                url: base_url+'/tree_ajax',
                data: {
                    id: node.data.key
                }
            });
        }
    });
});

icms.events.on('datagrid-inline-save-row', function(data) {

    let tree = $("#datatree").dynatree("getTree");

    let activeNode = tree.getActiveNode();

    let keys = activeNode.data.key.split('.');

    keys[1] = data.row.id;

    let nodeToUpdate = tree.getNodeByKey(keys.join('.'));

    if (nodeToUpdate) {
        nodeToUpdate.data.title = data.params.value;
        nodeToUpdate.render();
    }
});

icms.events.on('datagrid-inline-save-rows', function(data) {

    let tree = $("#datatree").dynatree("getTree");

    let activeNode = tree.getActiveNode();

    let keys = activeNode.data.key.split('.');

    for (let row_id in data.rows) {

        if (!data.rows[row_id].title) {
            continue;
        }

        keys[1] = row_id;

        let nodeToUpdate = tree.getNodeByKey(keys.join('.'));

        if (nodeToUpdate) {
            nodeToUpdate.data.title = data.rows[row_id].title;
            nodeToUpdate.render();
        }
    }
});