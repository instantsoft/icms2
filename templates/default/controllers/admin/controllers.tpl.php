<?php

    $this->setPageTitle(LANG_CP_SECTION_CONTROLLERS);

    $this->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->href_to('controllers'));

	$this->addToolButton(array(
		'class' => 'install',
		'title' => LANG_CP_INSTALL_PACKAGE,
		'href'  => $this->href_to('install')
	));

    $this->addToolButton(array(
        'class' => 'addons',
        'title' => LANG_CP_OFICIAL_ADDONS,
        'href'  => $this->href_to('addons_list')
    ));

	$this->addToolButton(array(
		'class' => 'logs',
		'title' => LANG_EVENTS_MANAGEMENT,
		'href'  => $this->href_to('controllers', array('events'))
	));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COMPONENTS
	));

?>

<h1><?php echo LANG_CP_SECTION_CONTROLLERS; ?></h1>

<?php $this->renderGrid($this->href_to('controllers', array('ajax')), $grid); ?>
