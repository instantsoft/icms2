<?php

    $this->addTplJSName('users');

    $this->setPageTitle(LANG_USERS_EDIT_PROFILE);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'icon'  => 'undo',
        'title' => LANG_CANCEL,
        'href'  => $cancel_url
    ]);

    $this->addBreadcrumb(LANG_USERS_EDIT_PROFILE);

    $this->renderChild('profile_edit_header', ['profile' => $profile]);

    if(!empty($profile['id']) && $profile['slug'] == $profile['id']){ $profile['slug'] = null; }

    $this->renderForm($form, $profile, [
        'action'  => '',
        'cancel'  => ['show' => true, 'href' => $cancel_url],
        'buttons' => !$allow_delete_profile ? [] : [
            [
                'title'      => LANG_USERS_DELETE_PROFILE,
                'as_link'    => true,
                'href'       => href_to_profile($profile, ['delete']),
                'attributes' => [
                    'class' => 'ajax-modal delete_profile mt-3 mt-md-0 float-md-right btn btn-danger'
                ]
            ]
        ],
        'method'  => 'post',
        'toolbar' => false
    ], $errors);
