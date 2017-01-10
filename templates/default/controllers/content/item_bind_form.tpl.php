<?php

    $page_title = sprintf(LANG_CONTENT_BIND_ITEM, $child_ctype['labels']['create']);

	$this->setPageTitle($page_title, $item['title']);

    $base_url = $ctype['name'];

    if ($ctype['options']['list_on']){
        $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
        $this->addBreadcrumb($list_header, href_to($base_url));
    }

    if (isset($item['category'])){
        foreach($item['category']['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
        }
    }

    $this->addBreadcrumb($item['title'], href_to($ctype['name'], $item['slug'] . '.html'));
    $this->addBreadcrumb($page_title);

?>

<div id="content_bind_form" data-filter-url="<?php echo href_to($ctype['name'], 'bind_list', array($child_ctype['name'], $item['id'])); ?>" data-mode="<?php echo $mode; ?>">

    <div class="find gui-panel">
        <div class="field">
			<?php echo html_select('item-find-field', $filter_fields, '', array('id'=>'item-find-field')); ?>
            <?php echo html_input('text', 'item-find-input', '', array('id'=>'item-find-input', 'placeholder' => LANG_CONTENT_BIND_ITEM_FIND_HINT)); ?>
        </div>
        <div class="loading-icon" style="display: none"></div>
    </div>

	<div class="filter-tabs">
		<ul class="pills-menu">
			<li class="active">
				<a href="#own" data-mode="own"><?php echo LANG_CONTENT_OWN_ITEMS; ?></a>
			</li>
			<li>
				<a href="#all" data-mode="all"><?php echo LANG_ALL; ?></a>
			</li>
		</ul>
	</div>

	<div class="result-pane">
		<div class="loading"></div>
	</div>

	<div class="buttons">
        <?php $button_title = $mode == 'unbind' ? LANG_CONTENT_UNBIND_ITEMS : LANG_CONTENT_BIND_ITEMS; ?>
		<input type="submit" name="submit" class="button-submit" value="<?php echo $button_title; ?>" data-title="<?php echo $button_title; ?>">
	</div>

    <form action="<?php echo href_to($ctype['name'], $mode == 'unbind' ? 'unbind' : 'bind', $child_ctype['name']); ?>" method="post" style="display:none">
        <input type="hidden" class="item_id" name="id" value="<?php echo $item['id']; ?>">
        <input type="hidden" class="selected_ids" name="selected_ids" value="<?php echo $item['id']; ?>">
    </form>

</div>

<?php if ($mode == 'childs' || $mode == 'unbind') { ?>
    <?php $this->insertJS('templates/default/js/content-bind.js'); ?>
    <script>icms.contentBind.start();</script>
<?php } ?>


