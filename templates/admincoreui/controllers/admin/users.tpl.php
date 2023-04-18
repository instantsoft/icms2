<?php

    $this->addTplJSName([
        'datatree'
    ]);

    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_USERS);

    $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));

    if (cmsController::enabled('messages')) {
        $this->addMenuItem('breadcrumb-menu', [
            'title'   => LANG_CP_USER_PMAILING,
            'url'     => $this->href_to('controllers', ['edit', 'messages', 'pmailing']),
            'options' => [
                'icon' => 'envelope-open-text'
            ]
        ]);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title'   => LANG_CONFIG,
        'url'     => $this->href_to('controllers', ['edit', 'users']),
        'options' => [
            'icon' => 'cog'
        ]
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title'   => LANG_HELP,
        'url'     => LANG_HELP_URL_USERS,
        'options' => [
            'target' => '_blank',
            'icon'   => 'question-circle'
        ]
    ]);
?>
<div class="row flex-nowrap align-items-stretch mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span>×</span></a>
        <div id="datatree" class="bg-white h-100 pt-3 pb-3 pr-3">
            <ul id="treeData" class="skeleton-tree">
                <?php foreach ($groups as $id => $group) { ?>
                    <li id="<?php echo $group['id'];?>" class="folder">
                        <?php echo $group['title']; ?>
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
                let path = $.cookie('icms[users_tree_path]');
                if (!path) { path = '/0'; }
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
                    icms.datagrid.setURL("<?php echo $this->href_to('users'); ?>/" + node.data.key);
                    icms.datagrid.loadRows();
                }
                is_init = true;
                node.expand();
                $.cookie('icms[users_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                let key = +node.data.key;
                $('.cp_toolbar .users_add a').attr('href', "<?php echo $this->href_to('users', 'add'); ?>/" + key);
                if (key === 0){
                    $('.cp_toolbar .edit_user a').hide();
                    $('.cp_toolbar .group_perms').addClass('d-none');
                    $('.cp_toolbar .delete_user a').hide();
                } else {
                    $('.cp_toolbar .users_group').addClass('animate-shake');
                    $('.cp_toolbar .edit_user a').show().attr('href', "<?php echo $this->href_to('users', 'group_edit'); ?>/" + key);
                    $('.cp_toolbar .group_perms').removeClass('d-none').find('a').attr('href', "<?php echo $this->href_to('users', 'group_perms'); ?>/" + key);
                    $('.cp_toolbar .delete_user a').show().attr('href', "<?php echo $this->href_to('users', 'group_delete'); ?>/" + key + '?csrf_token='+icms.forms.getCsrfToken());
                }
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
