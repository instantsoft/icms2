<?php

    $this->setPageTitle(LANG_USERS_EDIT_PROFILE_NOTICES);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb(LANG_USERS_EDIT_PROFILE, href_to('users', $id, 'edit'));
    $this->addBreadcrumb(LANG_USERS_EDIT_PROFILE_NOTICES);

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
        'href'  => href_to_profile($profile)
    ));

?>

<?php $this->renderChild('profile_edit_header', array('profile'=>$profile)); ?>

<?php
    $this->renderForm($form, $options, array(
        'action' => '',
        'method' => 'post',
        'toolbar' => false
    ), $errors);
