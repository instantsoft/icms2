<h1><?php echo LANG_CP_SECTION_SETTINGS; ?></h1>

<?php

    $this->setPageTitle(LANG_CP_SCHEDULER);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SCHEDULER);

    $this->addMenuItems('settings', $this->controller->getSettingsMenu());

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

?>

<div class="pills-menu">
    <?php $this->menu('settings', true, 'nav-pills'); ?>
</div>

<?php

    $this->renderGrid($this->href_to('settings', array('scheduler', 'ajax')), $grid);
