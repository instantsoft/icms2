<?php

    if ($do=='add') { $page_title = LANG_CP_SCHEDULER_TASK_ADD; }
    if ($do=='edit') { $page_title = LANG_CP_SCHEDULER_TASK_EDIT; }

    $this->setPageTitle($page_title);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SCHEDULER, $this->href_to('settings', array('scheduler')));
    $this->addBreadcrumb($page_title);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('settings', array('scheduler'))
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_SETTINGS_SCHEDULER_TASK
	));

?>

<h1><?php echo $page_title; ?></h1>

<?php
    $this->renderForm($form, $task, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
