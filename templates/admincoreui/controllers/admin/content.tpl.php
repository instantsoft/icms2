<?php
    $this->addTplJSName([
        'datatree',
        'admin-content'
    ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_CONTENT);

    $this->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->href_to('content'));

	$this->addToolButton(array(
        'class' => 'menu',
		'data'  => [
            'toggle' =>'quickview',
            'toggle-element' => '#left-quickview'
        ],
		'title' => LANG_CATEGORIES
	));

    $this->addToolButton(array(
        'class' => 'help',
        'title' => LANG_HELP,
        'target' => '_blank',
        'href'  => LANG_HELP_URL_CONTENT,
    ));

    $this->addToolButton(array(
        'class' => 'filter',
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
        'class' => 'settings',
        'title' => LANG_CONFIG,
        'href'  => $this->href_to('ctypes', array('edit'))
    ));

    $this->addToolButton(array(
        'class' => 'logs',
        'title' => LANG_MODERATION_LOGS,
        'href'  => $this->href_to('controllers', array('edit', 'moderation', 'logs', 'content'))
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
        'class' => 'tree_folder',
        'title' => LANG_CP_CONTENT_CATS_ORDER,
        'href'  => $this->href_to('content', array('cats_order')),
        'onclick' => 'return contentCatsReorder($(this))'
    ));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_CONTENT_ITEM_ADD,
        'href'  => $this->href_to('content', array('item_add'))
    ));

    $this->addToolButton(array(
        'class' => 'move show_on_selected',
        'title' => LANG_MOVE,
        'href'  => null,
        'onclick' => 'return icms.datagrid.submitAjax($(this))'
    ));

    $this->addToolButton(array(
        'class' => 'delete show_on_selected',
        'title' => LANG_DELETE,
        'href'  => null,
        'onclick' => "return icms.datagrid.submit($(this), '".LANG_DELETE_SELECTED_CONFIRM."')",
    ));

    $this->addToolButton(array(
        'class' => 'basket_put show_on_selected',
        'title' => LANG_BASKET_DELETE,
        'href'  => null,
        'onclick' => "return icms.datagrid.submit($(this), '".LANG_TRASH_DELETE_SELECTED_CONFIRM."')",
    ));

    $this->applyToolbarHook('admin_content_toolbar');

?>

<h1><?php echo LANG_CP_SECTION_CONTENT; ?> <span class="text-muted"><?php if($ctype){ echo $ctype['title']; } ?></span></h1>

<div class="row align-items-stretch">
    <div class="col-md-2" id="left-quickview">
        <div id="datatree" class="card-body bg-white h-100">
            <ul id="treeData">
                <?php foreach($ctypes as $id=>$ctype){ ?>
                    <li id="<?php echo $ctype['id'];?>.1" class="lazy folder"><?php echo $ctype['title']; ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col-md-10">
        <?php $this->renderGrid(false, $grid); ?>
    </div>
</div>

<script type="text/javascript">
    $(function(){

        $('.cp_toolbar .delete_filter a').hide();
        var current_ctype = <?php echo $ctype_id; ?>;
        is_loaded = false;

        $('#datagrid_filter').append('<?php echo html_input('hidden', 'ctype_changed'); ?>');

        $("#datatree").dynatree({

            debugLevel: 0,

            onPostInit: function(isReloading, isError){
                var path = '<?php echo $key_path; ?>';
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
                $.cookie('icms[content_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                var key = node.data.key.split('.');
                icms.datagrid.setURL("<?php echo $this->href_to('content', array('items_ajax')); ?>/" + key[0] + "/" + key[1]);
                $('.cp_toolbar .filter a').attr('href', "<?php echo $this->href_to('content', array('filter')); ?>/" + key[0]);
                $('.cp_toolbar .settings a').attr('href', "<?php echo $this->href_to('ctypes', array('edit')); ?>/" + key[0]);
                $('.cp_toolbar .add a').attr('href', "<?php echo $this->href_to('content', array('item_add')); ?>/" + key[0] + "/" + key[1]);
                $('.cp_toolbar .add_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_add')); ?>/" + key[0] + "/" + key[1]);
                $('.cp_toolbar .edit_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_edit')); ?>/" + key[0] + "/" + key[1]);
                $('.cp_toolbar .delete_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_delete')); ?>/" + key[0] + "/" + key[1] + '?csrf_token='+icms.forms.getCsrfToken());
                $('.cp_toolbar .tree_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_order')); ?>/" + key[0]);
                $('.cp_toolbar .move a').data('url', "<?php echo $this->href_to('content', array('item_move')); ?>/" + key[0] + "/" + key[1]);
                $('.cp_toolbar .delete a').data('url', "<?php echo $this->href_to('content', array('item_delete')); ?>/" + key[0] + '?csrf_token='+icms.forms.getCsrfToken());
                $('.cp_toolbar .basket_put a').data('url', "<?php echo $this->href_to('content', array('item_trash_put')); ?>/" + key[0] + '?csrf_token='+icms.forms.getCsrfToken());
                $('.cp_toolbar .logs a').attr('href', "<?php echo $this->href_to('controllers', array('edit', 'moderation', 'logs', 'content')); ?>/" + key[0]);
                if (key[1] == 1){
                    $('.cp_toolbar .edit_folder a').hide();
                    $('.cp_toolbar .delete_folder a').hide();
                } else {
                    $('.cp_toolbar .edit_folder a').show();
                    $('.cp_toolbar .delete_folder a').show();
                }
                var root_node = null;
                node.visitParents(function (_node) {
                    if(_node.parent !== null){
                        root_node = _node;
                    }
                }, true);
                $('h1 > span').html(root_node.data.title);
                $('.nav-item.item-content a').removeClass('active');
                $('a[title='+root_node.data.title+']').addClass('active');
                if(key[0] !== current_ctype){
                    current_ctype = key[0];
                    contentCancelFilter(true);
                }else{
                    icms.datagrid.loadRows();
                }
                $('.datagrid > tbody > tr.filter > td:last').html('<a title="<?php echo LANG_CP_GRID_COLYMNS_SETTINGS; ?>" class="columns_settings" href="<?php echo $this->href_to('content', array('grid_columns')); ?>/'+key[0]+'" onclick="return icms.modal.openAjax($(this).attr(\'href\'), {}, undefined, \'<?php echo LANG_CP_GRID_COLYMNS_SETTINGS; ?>\')"><i class="icon-settings"></i></a>');
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
        icms.datagrid.callback = function (){
            $('#datagrid td > span[rel = set_class]').each(function(indx){
                $(this).parents('tr').addClass($(this).data('class'));
            });
        };
        $('.datagrid').tooltip({
          items: 'td > a:has(.grid_image_preview)',
          content: function(){
            var element = $(this);
            if(element.is('a')){
              return '<img class="datagrid_image_preview" alt="" src="'+element.attr('href')+'" />';
            }
          },
          position: {
              using: function(position, feedback){
                  position['max-width'] = '500px';
                  $(this).css(position);
              }
          },
          hide: {duration: 0},
          show: {duration: 0}
        });
    });

</script>