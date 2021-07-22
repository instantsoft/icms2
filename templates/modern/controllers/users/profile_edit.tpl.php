<?php

    $this->addTplJSName('users');

    $this->setPageTitle(LANG_USERS_EDIT_PROFILE);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));

    $this->addToolButton(array(
        'class' => 'save',
        'icon'  => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'icon'  => 'window-close',
        'title' => LANG_CANCEL,
        'href'  => $cancel_url
    ));

    $this->addBreadcrumb(LANG_USERS_EDIT_PROFILE);

    $this->renderChild('profile_edit_header', array('profile'=>$profile));

    if(!empty($profile['id']) && $profile['slug'] == $profile['id']){ $profile['slug'] = null; }
    $this->renderForm($form, $profile, array(
        'action'  => '',
        'cancel'  => array('show' => true, 'href' => $cancel_url),
        'buttons' => !$allow_delete_profile ? [] : array(
            array(
                'title'      => LANG_USERS_DELETE_PROFILE,
                'name'       => 'delete_profile',
                'onclick'    => "icms.users.delete('" . href_to_profile($profile, ['delete']) . "', '" . LANG_USERS_DELETE_PROFILE . "');",
                'attributes' => array(
                    'class' => 'delete_profile mt-3 mt-md-0 float-md-right btn-danger'
                )
            )
        ),
        'method'  => 'post',
        'toolbar' => false
    ), $errors);
