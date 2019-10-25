<?php if (empty($categories)){ ?>
    <div class="alert alert-warning m-0" role="alert" id="alert_wrap">
        <?php echo LANG_CP_CONTENT_CATS_NONE; ?>
    </div>
<?php } else { ?>

<div class="modal_padding">
    <form action="<?php echo $this->href_to('content', array('cats_order', $ctype['id'])); ?>" onsubmit="return contentSaveCatsOrder($(this))" method="post">
        <fieldset class="modal_treeview mb-4">
            <legend><?php echo LANG_CP_CONTENT_CATS_ORDER_DRAG; ?></legend>
            <div id="ordertree" class="no-overflow">
                <ul id="treeData">

                    <?php $last_level = 0; ?>

                    <?php foreach($categories as $id=>$item){ ?>

                        <?php
                            if (!isset($item['ns_level'])) { $item['ns_level'] = 1; }
                            $item['childs_count'] = ($item['ns_right'] - $item['ns_left']) > 1;
                        ?>

                        <?php for ($i=0; $i<($last_level - $item['ns_level']); $i++) { ?>
                            </li></ul>
                        <?php } ?>

                        <?php if ($item['ns_level'] <= $last_level) { ?>
                            </li>
                        <?php } ?>

                        <li class="folder" id="<?php echo $id; ?>">

                                <?php html($item['title']); ?>

                            <?php if ($item['childs_count']) { ?><ul><?php } ?>

                        <?php $last_level = $item['ns_level']; ?>

                    <?php } ?>

                    <?php for ($i=0; $i<$last_level; $i++) { ?>
                        </li></ul>
                    <?php } ?>
            </div>
        </fieldset>

        <?php echo html_input('hidden', 'hash', ''); ?>
        <?php echo html_submit(LANG_SAVE); ?>
    </form>

    <script type="text/javascript">
        $("#ordertree").dynatree({
            minExpandLevel: 2,
            expand: true,
            dnd: {
                onDragStart: function(node) {
                    return true;
                },
                autoExpandMS: 1000,
                preventVoidMoves: true,
                onDragEnter: function(node, sourceNode) {
                    return true;
                },
                onDragOver: function(node, sourceNode, hitMode) {
                    if(node.isDescendantOf(sourceNode)){ return false; }
                    if( !node.data.isFolder && hitMode === "over" ){ return "after"; }
                },
                onDrop: function(node, sourceNode, hitMode, ui, draggable) {
                    sourceNode.move(node, hitMode);
                    node.expand(true);
                },
                onDragLeave: function(node, sourceNode) {
                }
            }
        });

        function contentSaveCatsOrder(form){

            var dict = $('#ordertree').dynatree('getTree').toDict();
            $('input:hidden', form).val( JSON.stringify(dict) );
            return true;

        }
    </script>

</div>
<?php } ?>