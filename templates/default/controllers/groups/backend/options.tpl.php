<?php

    $this->addBreadcrumb(LANG_OPTIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_GROUPS
	));

?>

<?php
    $this->renderForm($form, $options, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>
