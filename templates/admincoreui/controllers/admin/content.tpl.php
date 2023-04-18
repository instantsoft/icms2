<?php
    $this->addTplJSName([
        'datatree',
        'admin-content'
    ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_CONTENT);

    $this->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->href_to('content'));
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('content').'?last');

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CONTENT,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);
?>

<div class="row flex-nowrap align-items-stretch mb-4">
    <div class="col-sm col-xl-3 col-xxl-2 quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span>×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3">
            <ul id="treeData" class="skeleton-tree">
                <?php foreach ($ctypes as $id => $_ctype) { ?>
                    <li id="<?php echo $_ctype['id']; ?>.1" class="lazy folder">
                        <?php echo $_ctype['title']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col-sm-12 col-xl-9 col-xxl-10">
        <?php echo $grid_html ?>
    </div>
</div>

<script>
icms.events.on('datagrid_mounted', function(gridApp){
    let current_ctype_id = '<?php echo $ctype['id']; ?>';
    let cp_toolbar = $('.cp_toolbar');
    let is_init = false;
    $("#datatree").dynatree({
        debugLevel: 0,
        onPostInit: function(isReloading, isError){
            let path = '<?php echo $key_path; ?>';
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
            icms.datagrid.setURL('<?php echo $this->href_to('content'); ?>/' + key[0] + '/' + key[1]);
            if (is_init) {
                if(key[0] !== current_ctype_id){
                    current_ctype_id = key[0];
                    gridApp.filter = {};
                }
                icms.datagrid.loadRows();
            }
            is_init = true;
            gridApp.select_actions_items_map = key;
            $('.settings a', cp_toolbar).attr('href', "<?php echo $this->href_to('ctypes', ['edit']); ?>/" + key[0]);
            $('.logs a', cp_toolbar).attr('href', "<?php echo $this->href_to('controllers', ['edit', 'moderation', 'logs', 'content']); ?>/" + key[0]);
            $('.add_folder a', cp_toolbar).attr('href', "<?php echo $this->href_to('content', ['cats_add']); ?>/" + key[0] + "/" + key[1]);
            $('.edit_folder a', cp_toolbar).attr('href', "<?php echo $this->href_to('content', ['cats_edit']); ?>/" + key[0] + "/" + key[1]);
            $('.delete_folder a', cp_toolbar).attr('href', "<?php echo $this->href_to('content', ['cats_delete']); ?>/" + key[0] + "/" + key[1] + '?csrf_token='+icms.forms.getCsrfToken());
            $('.tree_folder a', cp_toolbar).attr('href', "<?php echo $this->href_to('content', ['cats_order']); ?>/" + key[0]);
            $('.add.add_site a', cp_toolbar).attr('href', "<?php echo $this->href_to('content', ['item_add']); ?>/" + key[0] + "/" + key[1]);
            $('.add.add_cpanel a', cp_toolbar).attr('href', "<?php echo $this->href_to('content', ['item_add']); ?>/" + key[0] + "/" + key[1] + "/1");

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
            window.history.pushState(null, null, '<?php echo $this->href_to('content'); ?>/'+key[0]);
        },
        onLazyRead: function(node){
            node.appendAjax({
                url: "<?php echo $this->href_to('content', ['tree_ajax']); ?>",
                data: {
                    id: node.data.key
                }
            });
        }
    });
});
</script>