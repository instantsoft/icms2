<?php
    $this->addTplJSName([
        'datatree',
        'admin-props'
    ]);

    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_CTYPE_PROPS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_PROPS);
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('ctypes').'?last');

    if ($cats){

        $this->addToolButton([
            'class' => 'menu d-xl-none',
            'data'  => [
                'toggle' =>'quickview',
                'toggle-element' => '#left-quickview'
            ],
            'title' => LANG_CATEGORIES
        ]);

        $this->addToolButton([
            'class' => 'add datagrid_change',
            'title' => LANG_CP_FIELD_ADD,
            'data'  => [
                'href' => $this->href_to('ctypes', ['props_add'])
            ]
        ]);

        $this->addToolButton([
            'class' => 'add_folder datagrid_change',
            'title' => LANG_CP_CONTENT_CATS_ADD,
            'data'  => [
                'href'  => $this->href_to('content', ['cats_add'])
            ]
        ]);

        $this->addToolButton([
            'class' => 'edit_folder datagrid_change',
            'title' => LANG_CP_CONTENT_CATS_EDIT,
            'data'  => [
                'href'  => $this->href_to('content', ['cats_edit'])
            ]
        ]);

        $this->addToolButton([
            'class' => 'delete_folder datagrid_change datagrid_csrf',
            'title' => LANG_DELETE_CATEGORY,
            'data'  => [
                'href'  => $this->href_to('content', ['cats_delete'])
            ],
            'confirm' => LANG_DELETE_CATEGORY_CONFIRM
        ]);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_PROPS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

?>

<?php if (!$cats){ ?>
    <p class="alert alert-info mt-4">
        <?php printf(LANG_CP_PROPS_NO_CATS, $ctype['title']); ?>
    </p>
    <p class="alert alert-success mt-4">
        <?php printf(LANG_CP_PROPS_NO_CATS_ADD, $this->href_to('content', ['cats_add', $ctype['id'], 1]) . '?back=' . href_to_current()); ?>
    </p>
    <?php return; ?>
<?php } ?>

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
<?php ob_start(); ?>
<script>

    icms.adminProps.cookie_path_key = 'icms[props<?php echo $ctype['id']; ?>_tree_path]';
    icms.adminProps.props_bind_url = '<?php echo $this->href_to('ctypes', ['props_bind']); ?>';
    icms.adminProps.datagrid_url = '<?php echo $this->href_to('ctypes', ['props', $ctype['id']]); ?>';
    icms.adminProps.tree_ajax_url = '<?php echo $this->href_to('content', ['tree_ajax']); ?>';
    icms.adminProps.back_url = '<?php echo $this->href_to('ctypes', ['props', $ctype['id']]) ?>';

</script>
<?php $this->addBottom(ob_get_clean()); ?>