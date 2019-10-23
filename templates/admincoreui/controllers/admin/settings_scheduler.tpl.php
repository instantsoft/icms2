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

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_SETTINGS_SCHEDULER,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->renderGrid($this->href_to('settings', array('scheduler', 'ajax')), $grid);
