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
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span>×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3">
            <ul id="treeData" class="skeleton-tree">
                <li id="<?php echo $ctype['id'];?>.0">
                    <?php echo LANG_ALL; ?>
                </li>
                <?php foreach ($cats as $id => $cat) { ?>
                    <li id="<?php echo $ctype['id'];?>.<?php echo $cat['id']; ?>" class="lazy folder">
                        <?php echo $cat['title']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php echo $grid_html; ?>

        <?php if ($props){ ?>
            <form action="" method="post" id="props-bind" style="display:none">
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
<script>
    icms.adminProps.cookie_path_key = 'icms[<?php echo $cookie_path_key; ?>]';
    icms.adminProps.tree_path_key   = '<?php echo $tree_path_key; ?>';
    icms.adminProps.props_bind_url  = '<?php echo $this->href_to('ctypes', ['props_bind', $ctype['id']]); ?>';
    icms.adminProps.datagrid_url    = '<?php echo $this->href_to('ctypes', ['props', $ctype['id']]); ?>';
    icms.adminProps.tree_ajax_url   = '<?php echo $this->href_to('content', ['tree_ajax']); ?>';
    icms.adminProps.back_url        = '<?php echo $this->href_to('ctypes', ['props', $ctype['id']]) ?>';

    icms.events.on('datagrid_mounted', function(gridApp){
        icms.adminProps.init(gridApp);
    });
</script>