<?php

    $this->addBreadcrumb(LANG_USERS_CFG_FIELDS);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_FIELD_ADD,
        'href'  => $this->href_to('fields_add')
    ));
    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE_ORDER,
        'href'  => null,
        'onclick' => "icms.datagrid.submit('{$this->href_to('fields_reorder')}')"
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_USERS
	));

?>

<?php $this->renderGrid($this->href_to('fields_ajax'), $grid); ?>

<div class="buttons">
    <?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('fields_reorder')}')"); ?>
</div>
