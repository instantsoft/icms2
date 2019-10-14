<?php
    $this->addTplJSName([
        'datatree',
        'admin-content'
    ]);

    $this->addTplCSSName('datatree');

    if(!empty($page_title)){
        $this->setPageTitle($page_title);
        $this->addBreadcrumb($page_title);
    }

    if(!empty($toolbar)){
        foreach ($toolbar as $menu_item) {
            $this->addToolButton($menu_item);
        }
    }

    $this->applyToolbarHook('admin_'.$contex_target.'_toolbar');

?>
<?php if(!empty($page_title)){ ?>
    <h1><?php echo $page_title; ?> <span class="text-muted"></span></h1>
<?php } ?>

<div class="row grid-layout align-content-around">
    <div class="col-2">
        <div class="card mb-0 h-100">
            <div id="datatree" class="card-body">
                <ul id="treeData">
                    <?php foreach($tree as $tree_item){ ?>
                        <li id="<?php echo $tree_item['id'];?>.1" class="<?php if(!empty($tree_is_tree)){ ?>lazy <?php } ?>folder">
                            <?php echo $tree_item['title']; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-10">
        <?php $this->renderGrid(false, $grid); ?>
        <?php if(!empty($footer_html)) { ?>
            <?php echo $footer_html; ?>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $(function(){

        $('.cp_toolbar .delete_filter a').hide();
        var current_root_id = <?php echo $current_root_id; ?>;

        $("#datatree").dynatree({
            debugLevel: 0,
            onPostInit: function(isReloading, isError){
                var path = '<?php echo $key_path; ?>';
                $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                    if(status === 'loaded') {
                        node.expand();
                    } else if(status === 'ok') {
                        node.activate();
                        node.expand();
                        icms.datagrid.init();
                    }
                });
            },
            onActivate: function(node){
                node.expand();
                $.cookie('icms[<?php echo $contex_cookie_name; ?>]', node.getKeyPath(), {expires: 7, path: '/'});
                var key = node.data.key.split('.');
                icms.datagrid.setURL("<?php echo $grid_url; ?>/" + key[0] + "/" + key[1]);
                <?php if(!empty($cp_toolbar)) { ?>
                    <?php foreach($cp_toolbar as $ti){ ?>
                        $('.cp_toolbar .<?php echo $ti['classs']; ?> a').data('url', '<?php echo $ti['base_url']; ?>'<?php echo $ti['url_params']; ?>;
                    <?php } ?>
                <?php } ?>
                var root_node = null;
                node.visitParents(function (_node) {
                    if(_node.parent !== null){
                        root_node = _node;
                    }
                }, true);
                $('h1 > span').html(root_node.data.title);
                if(key[0] != current_root_id){
                    current_root_id = key[0];
                    contentCancelFilter(true);
                } else {
                    icms.datagrid.loadRows();
                }
            },
            onLazyRead: function(node){
                node.appendAjax({
                    url: "<?php echo $tree_ajax_url; ?>",
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
    });
</script>