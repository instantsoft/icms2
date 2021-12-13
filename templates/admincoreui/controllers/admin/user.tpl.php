<?php

    if ($do === 'add') { $this->setPageTitle(LANG_CP_USER_ADD); }
    if ($do === 'edit') { $this->setPageTitle(LANG_USER.': '.$user['nickname']); }

    if ($do === 'add'){
        $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
        $this->addBreadcrumb(LANG_CP_USER_ADD);
    }

    if ($do === 'edit'){
        $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
        $this->addBreadcrumb($user['nickname'].', '.$user['email']);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_USERS_USER,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->addToolButton([
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('users')
    ]);

    $this->renderForm($form, $user, [
        'action' => '',
        'method' => 'post'
    ], $errors);
