<?php

    $this->setPageTitle(LANG_USERS_MY_INVITES);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb(LANG_USERS_MY_INVITES);

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => href_to_profile($profile)
    ));


?>

<h1><?php echo LANG_USERS_MY_INVITES; ?></h1>

<?php
    $this->renderForm($form, $input, array(
        'action' => '',
        'method' => 'post',
        'toolbar' => false,
        'submit' => array(
            'title' => LANG_SEND
        )
    ), $errors);
