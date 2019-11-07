<?php

    $this->addTplJSName([
        'datatree'
    ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_MENU);

    $this->addBreadcrumb(LANG_CP_SECTION_MENU, $this->href_to('menu'));
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('menu').'?last');

	$this->addToolButton(array(
        'class' => 'menu d-xl-none',
		'data'  => [
            'toggle' =>'quickview',
            'toggle-element' => '#left-quickview'
        ],
		'title' => LANG_MENU
	));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_MENU_ITEM_ADD,
        'href'  => $this->href_to('menu', array('item_add', 1, 0))
    ));

    $this->addToolButton(array(
        'class' => 'add_folder',
        'title' => LANG_CP_MENU_ADD,
        'href'  => $this->href_to('menu', array('add'))
    ));

    $this->addToolButton(array(
        'class' => 'edit_folder',
        'title' => LANG_CP_MENU_EDIT,
        'href'  => $this->href_to('menu', array('edit'))
    ));

    $this->addToolButton(array(
        'class' => 'delete_folder',
        'title' => LANG_CP_MENU_DELETE,
        'confirm' => LANG_CP_MENU_DELETE_CONFIRM,
        'href'  => $this->href_to('menu', array('delete'))
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_MENU,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->applyToolbarHook('admin_menu_toolbar');

?>

<div class="row align-items-stretch mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span aria-hidden="true">×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3">
            <ul id="treeData">
                <?php foreach($menus as $id=>$menu){ ?>
                    <li id="<?php echo $menu['id'];?>.0" class="lazy folder"><?php echo $menu['title']; ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php $this->renderGrid($this->href_to('menu', array('items_ajax', 1, 0)), $grid); ?>
    </div>
</div>

<script type="text/javascript">
        $(function(){
            $("#datatree").dynatree({
                onPostInit: function(isReloading, isError){
                    var path = $.cookie('icms[menu_tree_path]');
                    if (!path) { path = '/1.0'; }
                    $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                        if(status == "loaded") {
                            node.expand();
                        }else if(status == "ok") {
                            node.activate();
                            node.expand();
                            icms.datagrid.init();
                        }
                    });
                },
                onActivate: function(node){
                    node.expand();
                    $.cookie('icms[menu_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                    var key = node.data.key.split('.');
                    icms.datagrid.setURL("<?php echo $this->href_to('menu', array('items_ajax')); ?>/" + key[0] + "/" + key[1]);
                    $('.cp_toolbar .add a').attr('href', "<?php echo $this->href_to('menu', array('item_add')); ?>/" + key[0] + "/" + key[1]);
                    $('.cp_toolbar .edit_folder a').attr('href', "<?php echo $this->href_to('menu', array('edit')); ?>/" + key[0]);
                    $('.cp_toolbar .delete_folder a').attr('href', "<?php echo $this->href_to('menu', array('delete')); ?>/" + key[0]);
                    icms.datagrid.loadRows();
                    $('.breadcrumb-item.active').html(node.data.title);
                },
                onLazyRead: function(node){
                    node.appendAjax({
                        url: "<?php echo $this->href_to('menu', array('tree_ajax')); ?>",
                        data: {
                            id: node.data.key
                        }
                    });
                }
            });
        });
</script>