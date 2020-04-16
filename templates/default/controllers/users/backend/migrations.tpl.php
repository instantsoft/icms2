<?php

    $this->addBreadcrumb(LANG_USERS_CFG_MIGRATION);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_USERS_MIG_ADD,
        'href'  => $this->href_to('migrations_add')
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_USERS
	));

?>

<?php $this->renderGrid($this->href_to('migrations_ajax'), $grid); ?>
