<?php
    $this->addCSS('templates/default/css/datatree.css');
    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
    $this->addJS('templates/default/js/datatree.js');
    $this->addJS('templates/default/js/admin-props.js');
?>

<h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1>

<?php
    $this->setPageTitle(LANG_CP_CTYPE_PROPS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_PROPS);

    $this->addMenuItems('ctype', $this->controller->getCtypeMenu('props', $ctype['id']));

    if ($cats){

        $this->addToolButton(array(
            'class' => 'add',
            'title' => LANG_CP_FIELD_ADD,
            'href'  => $this->href_to('ctypes', array('props_add', $ctype['name']))
        ));

        $this->addToolButton(array(
            'class' => 'add_folder',
            'title' => LANG_CP_CONTENT_CATS_ADD,
            'href'  => $this->href_to('content', array('cats_add'))
        ));

        $this->addToolButton(array(
            'class' => 'edit_folder',
            'title' => LANG_CP_CONTENT_CATS_EDIT,
            'href'  => $this->href_to('content', array('cats_edit'))
        ));

        $this->addToolButton(array(
            'class' => 'delete_folder',
            'title' => LANG_DELETE_CATEGORY,
            'href'  => $this->href_to('content', array('cats_delete')),
            'confirm' => LANG_DELETE_CATEGORY_CONFIRM
        ));

        $this->addToolButton(array(
            'class' => 'save',
            'title' => LANG_SAVE,
            'href'  => null,
            'onclick' => "icms.datagrid.submit('{$this->href_to('ctypes', array('props_reorder', $ctype['name']))}')"
        ));

        $this->addToolButton(array(
            'class' => 'help',
            'title' => LANG_HELP,
            'target' => '_blank',
            'href'  => LANG_HELP_URL_CTYPES_PROPS,
        ));

    }

?>

<div class="pills-menu">
    <?php $this->menu('ctype'); ?>
</div>

<?php if (!$cats){ ?>
    <p><?php printf(LANG_CP_PROPS_NO_CATS, $ctype['title']); ?></p>
    <p><?php printf(LANG_CP_PROPS_NO_CATS_ADD, $this->href_to('content', array('cats_add', $ctype['id'], 1)) . '?back=' . href_to_current()); ?></p>
<?php } ?>

<?php if ($cats){ ?>

<table class="layout-no-fit">
    <tr>
        <td class="sidebar" valign="top">

            <div id="datatree">
                <ul id="treeData" style="display: none">
                    <?php foreach($cats as $id=>$cat){ ?>
                        <li id="<?php echo $ctype['id'];?>.<?php echo $cat['id']; ?>" class="lazy folder"><?php echo $cat['title']; ?></li>
                    <?php } ?>
                </ul>
            </div>

            <script type="text/javascript">
                    $(function(){

                        $("#datatree").dynatree({

                            onPostInit: function(isReloading, isError){
                                var path = $.cookie('icms[props<?php echo $ctype['id']; ?>_tree_path]');
                                if (!path) { path = '1.1'; }
                                if (path) {
                                    $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                                        if(status == "loaded") {
                                            node.expand();
                                        }else if(status == "ok") {
                                            node.activate();
                                            node.expand();
                                            icms.datagrid.init();
                                        }
                                    });
                                }
                            },

                            onActivate: function(node){
                                node.expand();
                                $.cookie('icms[props<?php echo $ctype['id']; ?>_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                                var key = node.data.key.split('.');
                                $('.cp_toolbar .add a').attr('href', "<?php echo $this->href_to('ctypes', array('props_add')); ?>/" + key[0] + "/" + key[1]);
                                $('.cp_toolbar .add_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_add')); ?>/" + key[0] + "/" + key[1] + '?back=<?php echo $this->href_to('ctypes', array('props', $ctype['id'])) ?>');
                                $('.cp_toolbar .edit_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_edit')); ?>/" + key[0] + "/" + key[1] + '?back=<?php echo $this->href_to('ctypes', array('props', $ctype['id'])) ?>');
                                $('.cp_toolbar .delete_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_delete')); ?>/" + key[0] + "/" + key[1] + '?back=<?php echo $this->href_to('ctypes', array('props', $ctype['id'])) ?>');
                                $('form#props-bind').attr('action', "<?php echo $this->href_to('ctypes', array('props_bind')); ?>/" + key[0] + "/" + key[1]);
                                icms.datagrid.setURL("<?php echo $this->href_to('ctypes', array('props_ajax', $ctype['name'])); ?>/" + key[1]);
                                icms.datagrid.loadRows(filterPropsList);
                                if (node.bExpanded==false){
                                    $('#props-bind #is_childs .input-checkbox').removeAttr('checked');
                                    $('#props-bind #is_childs').hide();
                                } else {
                                    $('#props-bind #is_childs .input-checkbox').attr('checked', 'checked');
                                    $('#props-bind #is_childs').show();
                                }
                            },

                            onLazyRead: function(node){
                                node.appendAjax({
                                    url: "<?php echo $this->href_to('content', array('tree_ajax')); ?>",
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

            <?php $this->renderGrid($this->href_to('ctypes', array('props_ajax', $ctype['name'])), $grid); ?>

            <?php if ($props){ ?>
                <form action="" method="post" id="props-bind">
                    <div id="ctypes-props-toolbar">
                        <?php echo LANG_CP_PROPS_BIND; ?> &mdash;
                        <?php echo html_select('prop_id', array_collection_to_list($props, 'id', 'title')); ?>
                        <?php echo html_submit('+'); ?>
                        <label id="is_childs">
                            <?php echo html_checkbox('is_childs', true); ?>
                            <?php echo LANG_CP_PROPS_BIND_RECURSIVE; ?>
                        </label>
                    </div>
                </form>
                <div style="display:none">
                    <?php echo html_select('props_list', array_collection_to_list($props, 'id', 'title')); ?>
                </div>
            <?php } ?>

            <div class="buttons">
                <?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('ctypes', array('props_reorder', $ctype['name']))}')"); ?>
            </div>

        </td>
    </tr>
</table>

<?php } ?>
