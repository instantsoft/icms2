<?php

    $this->setPageTitle($do == 'add' ? LANG_CP_MENU_ADD : LANG_CP_MENU.': '.$item['title']);

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_MENU, $this->href_to('menu'));
        $this->addBreadcrumb(LANG_CP_MENU_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb(LANG_CP_MENU, $this->href_to('menu'));
        $this->addBreadcrumb($item['title']);
    }

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('menu'),
        'icon'  => 'undo'
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_MENU,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderForm($form, $item, [
        'action' => '',
        'method' => 'post'
    ], $errors);
