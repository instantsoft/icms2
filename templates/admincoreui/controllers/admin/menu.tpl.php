<?php

    $this->addTplJSName([
        'datatree'
    ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_MENU);

    $this->addBreadcrumb(LANG_CP_SECTION_MENU, $this->href_to('menu'));
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('menu').'?last');

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_MENU,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);
?>

<div class="row align-items-stretch mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span>×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3">
            <ul id="treeData" class="skeleton-tree">
                <?php foreach ($menus as $id => $menu) { ?>
                    <li id="<?php echo $menu['id'];?>.0" class="lazy folder">
                        <?php html($menu['title']); ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php echo $grid_html; ?>
    </div>
</div>

<script>
    icms.events.on('datagrid_mounted', function(app){
        let is_init = false;
        $("#datatree").dynatree({
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
                    icms.datagrid.setURL("<?php echo $this->href_to('menu'); ?>/" + node.data.key);
                    icms.datagrid.loadRows();
                }
                is_init = true;
                node.expand();
                $.cookie('icms[menu_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                let key = node.data.key.split('.');
                $('.cp_toolbar .add_item a').attr('href', "<?php echo $this->href_to('menu', ['item_add']); ?>/" + key[0] + "/" + key[1]);
                $('.cp_toolbar .edit_menu a').attr('href', "<?php echo $this->href_to('menu', ['edit']); ?>/" + key[0]);
                $('.cp_toolbar .delete_menu a').attr('href', "<?php echo $this->href_to('menu', ['delete']); ?>/" + key[0] + '?csrf_token='+icms.forms.getCsrfToken());
                $('.breadcrumb-item.active').html(node.data.title);
            },
            onLazyRead: function(node){
                node.appendAjax({
                    url: "<?php echo $this->href_to('menu', ['tree_ajax']); ?>",
                    data: {
                        id: node.data.key
                    }
                });
            }
        });
    });
</script>