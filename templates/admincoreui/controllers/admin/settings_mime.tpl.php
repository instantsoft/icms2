<?php

    $this->setPageTitle(LANG_CP_MIMETYPES);

    $this->addBreadcrumb(LANG_CP_MIMETYPES);

    $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_SETTINGS_MIME,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);
?>

<?php
    $this->renderForm($form, $data, [
        'action' => '',
        'method' => 'post',
    ], $errors);
?>