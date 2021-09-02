<?php

    $this->addTplJSName([
        'jquery-cookie',
        'datatree'
        ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_MENU);

    $this->addBreadcrumb(LANG_CP_SECTION_MENU, $this->href_to('menu'));

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

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE_ORDER,
        'href'  => null,
        'onclick' => "icms.datagrid.submit('{$this->href_to('menu', array('items_reorder'))}')"
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_MENU
	));

    $this->applyToolbarHook('admin_menu_toolbar');

?>

<h1><?php echo LANG_CP_SECTION_MENU; ?></h1>

<table class="layout">
    <tr>
        <td class="sidebar" valign="top">

            <div id="datatree">
                <ul id="treeData" style="display: none">
                    <?php foreach($menus as $id=>$menu){ ?>
                        <li id="<?php echo $menu['id'];?>.0" class="lazy folder"><?php echo $menu['title']; ?></li>
                    <?php } ?>
                </ul>
            </div>

            <script>
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

        </td>
        <td class="main" valign="top">

            <?php $this->renderGrid($this->href_to('menu', array('items_ajax', 1, 0)), $grid); ?>

            <div class="buttons">
                <?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('menu', array('items_reorder'))}')"); ?>
            </div>

        </td>
    </tr>
</table>

