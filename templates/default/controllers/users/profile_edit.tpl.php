<?php

    $this->setPageTitle(LANG_USERS_EDIT_PROFILE);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $id));

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => href_to('users', $id)
    ));

    $this->addBreadcrumb(LANG_USERS_EDIT_PROFILE);

?>

<?php $this->renderChild('profile_edit_header', array('profile'=>$profile)); ?>

<?php
    $this->renderForm($form, $profile, array(
        'action' => '',
        'method' => 'post',
        'toolbar' => false
    ), $errors);
?>
