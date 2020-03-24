<?php

    $this->addBreadcrumb(LANG_USERS_CFG_TABS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE_ORDER,
        'href'  => null,
        'onclick' => "icms.datagrid.submit('{$this->href_to('tabs_reorder')}')"
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_USERS
	));

?>

<?php $this->renderGrid($this->href_to('tabs_ajax'), $grid); ?>

<div class="buttons">
    <?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('tabs_reorder')}')"); ?>
</div>
