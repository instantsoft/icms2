<?php

    $this->setPageTitle(LANG_CP_CTYPE_LABELS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_LABELS);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'icon'  => 'list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_LABELS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderForm($form, $ctype, [
        'action' => '',
        'method' => 'post'
    ], $errors);
