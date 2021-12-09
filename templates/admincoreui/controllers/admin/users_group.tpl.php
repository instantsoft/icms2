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

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('users')
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_USERS_GROUP,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->setMenuItems('admin_toolbar', $menu);

    $this->renderForm($form, $group, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
