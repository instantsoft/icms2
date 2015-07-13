<?php

    $this->addBreadcrumb(LANG_PERMISSIONS);

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_GROUPS
	));

?>

<?php

    $submit_url = $this->href_to('perms_save', $subject ? $subject : false);

    echo $this->renderPermissionsGrid($rules, $groups, $values, $submit_url);
    