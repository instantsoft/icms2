<?php

    $this->addTplJSName([
        'datatree',
        'admin-content'
    ]);

    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_USERS);

    $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));

    if(cmsController::enabled('messages')){
        $this->addMenuItem('breadcrumb-menu', [
            'title' => LANG_CP_USER_PMAILING,
            'url' => $this->href_to('controllers', array('edit', 'messages', 'pmailing')),
            'options' => [
                'icon' => 'icon-envelope-letter'
            ]
        ]);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_CONFIG,
        'url' => $this->href_to('controllers', array('edit', 'users')),
        'options' => [
            'icon' => 'icon-settings'
        ]
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_USERS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

	$this->addToolButton(array(
        'class' => 'menu d-xl-none',
		'data'  => [
            'toggle' =>'quickview',
            'toggle-element' => '#left-quickview'
        ],
		'title' => LANG_GROUPS
	));

    $this->addToolButton(array(
        'class' => 'filter pl-md-0',
        'title' => LANG_FILTER,
        'href'  => null,
        'onclick' => "return icms.modal.openAjax($(this).attr('href'),{},false,$(this).attr('title'))"
    ));

    $this->addToolButton(array(
        'class' => 'delete_filter important',
        'title' => LANG_CANCEL,
        'href'  => null,
        'onclick' => "return contentCancelFilter()"
    ));

    $this->addToolButton(array(
        'class' => 'add_user',
        'title' => LANG_CP_USER_ADD,
        'href'  => $this->href_to('users', 'add')
    ));

    $this->addToolButton(array(
        'class' => 'users animated',
        'childs_count' => 4,
        'title' => LANG_GROUP,
        'href'  => ''
    ));

    $this->addToolButton(array(
        'class' => 'add_folder',
        'level' => 2,
        'title' => LANG_CP_USER_GROUP_ADD,
        'href'  => $this->href_to('users', 'group_add')
    ));

    $this->addToolButton(array(
        'class' => 'edit',
        'level' => 2,
        'title' => LANG_CP_USER_GROUP_EDIT,
        'href'  => $this->href_to('users', 'group_edit')
    ));

    $this->addToolButton(array(
        'class' => 'permissions',
        'level' => 2,
        'title' => LANG_CP_USER_GROUP_PERMS,
        'href'  => $this->href_to('users', 'group_perms')
    ));

    $this->addToolButton(array(
        'class' => 'delete',
        'level' => 2,
        'title' => LANG_CP_USER_GROUP_DELETE,
        'href'  => $this->href_to('users', 'group_delete'),
        'onclick' => "return confirm('".LANG_CP_USER_GROUP_DELETE_CONFIRM."')"
    ));

    $this->applyToolbarHook('admin_users_toolbar');

?>
<div class="row align-items-stretch mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span aria-hidden="true">×</span></a>
        <div id="datatree" class="bg-white h-100 pt-3 pb-3 pr-3">
            <ul id="treeData">
                <?php foreach($groups as $id=>$group){ ?>
                    <li id="<?php echo $group['id'];?>" class="folder"><?php echo $group['title']; ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php $this->renderGrid(false, $grid); ?>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $(document).on('click', '.datagrid .filter_ip', function (){
            $('#filter_ip').val($(this).text()).trigger('input');
            return false;
        });
        $('.cp_toolbar .delete_filter a').hide();
        var dtree = $("#datatree").dynatree({
            onPostInit: function(isReloading, isError){
                var path = $.cookie('icms[users_tree_path]');
                if (!path) { path = '/0'; }
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
                $.cookie('icms[users_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                var key = node.data.key;
                icms.datagrid.setURL("<?php echo $this->href_to('users'); ?>/" + key);
                $('.cp_toolbar .filter a').attr('href', "<?php echo $this->href_to('users', array('filter')); ?>/" + key[0]);
                $('.cp_toolbar .add_user a').attr('href', "<?php echo $this->href_to('users', 'add'); ?>/" + key);
                if (key == 0){
                    $('.cp_toolbar .edit a').hide();
                    $('.cp_toolbar .permissions a').hide();
                    $('.cp_toolbar .delete a').hide();
                    $('.cp_toolbar .users').removeClass('animate-shake');
                } else {
                    $('.cp_toolbar .users').addClass('animate-shake');
                    $('.cp_toolbar .edit a').show().attr('href', "<?php echo $this->href_to('users', 'group_edit'); ?>/" + key);
                    $('.cp_toolbar .permissions a').show().attr('href', "<?php echo $this->href_to('users', 'group_perms'); ?>/" + key);
                    $('.cp_toolbar .delete a').show().attr('href', "<?php echo $this->href_to('users', 'group_delete'); ?>/" + key + '?csrf_token='+icms.forms.getCsrfToken());
                }
                icms.datagrid.loadRows();
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
                    var dict = $('#datatree').dynatree('getTree').toDict();
                    $.post('<?php echo $this->href_to('users', 'group_reorder'); ?>', {items: dict}, function(result){
                        toastr.success(result.success_text);
                    }, 'json');
                },
                onDragLeave: function(node, sourceNode) {}
            }
        });
    });
</script>