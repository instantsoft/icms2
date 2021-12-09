<?php

    $this->setPageTitle(LANG_CP_SECTION_CONTROLLERS);

    $this->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->href_to('controllers'));

    $this->addMenuItems('admin_toolbar', $this->controller->getAddonsMenu());

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_COMPONENTS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderGrid($this->href_to('controllers', array('ajax')), $grid);
