<?php

    $this->addBreadcrumb(LANG_CP_MENU.' «'.$menu['title'].'»', $this->href_to('menu'));

    if ($do === 'add') {
        $this->addBreadcrumb(LANG_CP_MENU_ITEM_ADD);
        $this->setPageTitle(LANG_CP_MENU_ITEM_ADD);
    }

    if ($do === 'edit') {
        $this->addBreadcrumb($item['title']);
        $this->setPageTitle(LANG_CP_MENU_ITEM . ': ' . $item['title']);
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
