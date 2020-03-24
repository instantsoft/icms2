<?php

    if ($do=='add') { $page_title = LANG_CP_SCHEDULER_TASK_ADD; }
    if ($do=='edit') { $page_title = LANG_CP_SCHEDULER_TASK_EDIT; }

    $this->setPageTitle($page_title);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SCHEDULER, $this->href_to('settings', array('scheduler')));
    $this->addBreadcrumb($page_title);

    $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());

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

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_SETTINGS_SCHEDULER_TASK,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->renderForm($form, $task, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
