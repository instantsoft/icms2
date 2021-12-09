<?php

    $this->setPageTitle(LANG_CP_SETTINGS_TEMPLATE_OPTIONS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SETTINGS_TEMPLATE_OPTIONS);

    if($template_name === $this->site_config->template){
        $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'save',
        'title' => sprintf(LANG_CP_SUBMIT_COMPILE, 'SCSS'),
        'href'  => "javascript:icms.forms.submit('.button.submit_compile')"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('settings')
    ));
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

    $this->renderForm($form, $options, array(
        'action' => '',
        'buttons' => [
            array(
                'title' => sprintf(LANG_CP_SUBMIT_COMPILE, 'SCSS'),
                'name' => 'submit_compile',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'btn-success submit_compile'
                )
            )
        ],
        'method' => 'post'
    ), $errors);
