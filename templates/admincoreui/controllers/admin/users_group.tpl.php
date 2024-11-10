<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_USER_GROUP_ADD); }
    if ($do=='edit') { $this->setPageTitle(LANG_USER_GROUP.': '.$group['title']); }

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
        $this->addBreadcrumb(LANG_CP_USER_GROUP_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
        $this->addBreadcrumb(LANG_USER_GROUP.': '.$group['title']);
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
        'href'  => $this->href_to('users'),
        'icon'  => 'undo'
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_USERS_GROUP,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->setMenuItems('admin_toolbar', $menu);

    $this->renderForm($form, $group, [
        'action' => '',
        'method' => 'post'
    ], $errors);
