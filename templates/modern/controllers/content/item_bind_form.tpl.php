<div id="content_bind_form" data-filter-url="<?php html($filter_url); ?>" data-mode="<?php html($mode); ?>">
    <div class="input-group find">
        <?php echo html_select('item-find-field', $filter_fields, '', array('id'=>'item-find-field')); ?>
        <?php echo html_input('text', 'item-find-input', '', array('id'=>'item-find-input', 'placeholder' => LANG_CONTENT_BIND_ITEM_FIND_HINT)); ?>
    </div>
    <ul class="nav nav-pills pills-menu my-3">
        <?php if($show_all_tab){ ?>
            <li class="nav-item">
                <a class="nav-link active position-relative" href="#all" data-mode="all">
                    <span><?php echo LANG_ALL; ?></span>
                </a>
            </li>
        <?php } ?>
        <?php if($show_my_tab){ ?>
            <li class="nav-item">
                <a class="nav-link position-relative<?php if(!$show_all_tab){ ?> active<?php } ?>" href="#own" data-mode="own">
                    <span><?php echo LANG_CONTENT_OWN_ITEMS; ?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
	<div class="result-pane"></div>
	<div class="buttons invisible">
        <?php $button_title = $input_action == 'bind' ? ($mode == 'unbind' ? LANG_CONTENT_UNBIND_ITEMS : LANG_CONTENT_BIND_ITEMS) : LANG_SELECT; ?>
		<input type="submit" name="submit" class="button-submit btn btn-primary" value="<?php echo $button_title; ?>" data-title="<?php echo $button_title; ?>">
	</div>
    <form action="<?php echo href_to($ctype['name'], $mode == 'unbind' ? 'unbind' : 'bind', $child_ctype['name']); ?>" method="post" style="display:none">
        <input type="hidden" class="item_id" name="id" value="<?php echo $item['id']; ?>">
        <input type="hidden" class="selected_ids" name="selected_ids" value="<?php echo $item['id']; ?>">
    </form>
</div>

<?php if ($mode == 'childs' || $mode == 'unbind') { ?>
    <?php $this->insertJS($this->getJavascriptFileName('content-bind')); ?>
    <script>icms.contentBind.start();</script>
<?php }
