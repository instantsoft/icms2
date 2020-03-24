<?php
    $this->addTplJSName([
        'datatree',
        'admin-props'
    ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_CTYPE_PROPS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_PROPS);
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('ctypes').'?last');

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('props', $ctype['id']));

    if ($cats){

        $this->addToolButton(array(
            'class' => 'menu d-xl-none',
            'data'  => [
                'toggle' =>'quickview',
                'toggle-element' => '#left-quickview'
            ],
            'title' => LANG_CATEGORIES
        ));

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

    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_PROPS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

?>

<?php if (!$cats){ ?>
    <p class="alert alert-info mt-4"><?php printf(LANG_CP_PROPS_NO_CATS, $ctype['title']); ?></p>
    <p class="alert alert-success mt-4"><?php printf(LANG_CP_PROPS_NO_CATS_ADD, $this->href_to('content', array('cats_add', $ctype['id'], 1)) . '?back=' . href_to_current()); ?></p>
<?php } ?>

<?php if ($cats){ ?>

<div class="row align-items-stretch mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span aria-hidden="true">×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3">
            <ul id="treeData">
                <li id="<?php echo $ctype['id'];?>.0"><?php echo LANG_ALL; ?></li>
                <?php foreach($cats as $id=>$cat){ ?>
                    <li id="<?php echo $ctype['id'];?>.<?php echo $cat['id']; ?>" class="lazy folder"><?php echo $cat['title']; ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php $this->renderGrid(false, $grid); ?>

        <?php if ($props){ ?>
            <form action="" method="post" id="props-bind">
                <div class="form-row align-items-center" id="ctypes-props-toolbar">
                    <div class="col-auto">
                        <?php echo html_select('prop_id', []); ?>
                    </div>
                    <div class="col-auto" id="is_childs">
                        <div class="form-check">
                            <?php echo html_checkbox('is_childs', true, 1, ['id' => 'props_bind_recursive']); ?>
                            <label class="form-check-label" for="props_bind_recursive">
                                <?php echo LANG_CP_PROPS_BIND_RECURSIVE; ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <?php echo html_submit(LANG_CP_PROPS_BIND); ?>
                    </div>
                </div>
            </form>
            <div style="display:none">
                <?php echo html_select('props_list', array_collection_to_list($props, 'id', 'title')); ?>
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $(function(){

        is_loaded = false;

        $("#datatree").dynatree({
            onPostInit: function(isReloading, isError){
                var path = $.cookie('icms[props<?php echo $ctype['id']; ?>_tree_path]');
                if (!path) {
                    $('a', "#datatree").eq(0).trigger('click');
                }
                if (path) {
                    $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                        if(status == "loaded") {
                            node.expand();
                        }else if(status == "ok") {
                            node.activate();
                            node.expand();
                        }
                    });
                }
            },
            onActivate: function(node){
                node.expand();
                $.cookie('icms[props<?php echo $ctype['id']; ?>_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                var key = node.data.key.split('.');
                $('.cp_toolbar .add').show();
                $('.cp_toolbar .edit_folder, .cp_toolbar .delete_folder').show();
                if(key[1] == 0){
                    hide_filter_props_list = true;
                    $('.cp_toolbar .add, .cp_toolbar .edit_folder, .cp_toolbar .delete_folder').hide();
                } else {
                    hide_filter_props_list = false;
                }
                $('.cp_toolbar .add a').attr('href', "<?php echo $this->href_to('ctypes', array('props_add')); ?>/" + key[0] + "/" + key[1]);
                $('.cp_toolbar .add_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_add')); ?>/" + key[0] + "/" + key[1] + '?back=<?php echo $this->href_to('ctypes', array('props', $ctype['id'])) ?>');
                $('.cp_toolbar .edit_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_edit')); ?>/" + key[0] + "/" + key[1] + '?back=<?php echo $this->href_to('ctypes', array('props', $ctype['id'])) ?>');
                $('.cp_toolbar .delete_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_delete')); ?>/" + key[0] + "/" + key[1] + '?back=<?php echo $this->href_to('ctypes', array('props', $ctype['id'])) ?>&csrf_token='+icms.forms.getCsrfToken());
                $('form#props-bind').attr('action', "<?php echo $this->href_to('ctypes', array('props_bind')); ?>/" + key[0] + "/" + key[1]);
                if (node.bExpanded==false){
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
                icms.datagrid.setURL("<?php echo $this->href_to('ctypes', array('props_ajax', $ctype['name'])); ?>/" + key[1]);
                icms.datagrid.loadRows(filterPropsList);
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
<?php } ?>