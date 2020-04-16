<?php

    $this->addBreadcrumb(LANG_USERS_CFG_MIGRATION, $this->href_to('migrations'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_USERS_MIG_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($rule['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('migrations')
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_USERS_MIGRATON
	));

?>

<?php
    $this->renderForm($form, $rule, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
