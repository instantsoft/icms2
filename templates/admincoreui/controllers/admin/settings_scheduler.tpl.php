<?php

    $this->setPageTitle(LANG_CP_SCHEDULER);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SCHEDULER);

    $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_SCHEDULER_TASK_ADD,
        'href'  => $this->href_to('settings', array('scheduler', 'add'))
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_SETTINGS_SCHEDULER
	));

    $this->renderGrid($this->href_to('settings', array('scheduler', 'ajax')), $grid);
