<?php

    $this->setPageTitle(LANG_CP_SETTINGS_TEMPLATE_OPTIONS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SETTINGS_TEMPLATE_OPTIONS . (!empty($manifest['title']) ? ': ' . $manifest['title'] : ''));

    if($template_name === $this->site_config->http_template){
        $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());
    }

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'save process-save',
        'icon'  => 'fire',
        'title' => sprintf(LANG_CP_SUBMIT_COMPILE, 'SCSS'),
        'data'  => ['submit_class' => '.button.submit_compile'],
        'href'  => '#'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('settings'),
        'icon'  => 'undo'
    ]);

    if(!empty($manifest['author']['help'])){
        $this->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => $manifest['author']['help'],
            'options' => [
                'target' => '_blank',
                'icon' => 'question-circle'
            ]
        ]);
    }

    $this->renderForm($form, $options, [
        'action' => '',
        'buttons' => [
            [
                'title' => sprintf(LANG_CP_SUBMIT_COMPILE, 'SCSS'),
                'name' => 'submit_compile',
                'attributes' => [
                    'type' => 'submit',
                    'class' => 'btn-success submit_compile'
                ]
            ]
        ],
        'method' => 'post'
    ], $errors);
