<?php

    $this->setPageTitle(LANG_USERS_MY_INVITES);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $id));
    $this->addBreadcrumb(LANG_USERS_MY_INVITES);

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => href_to('users', $id)
    ));


?>

<h1><?php echo LANG_USERS_MY_INVITES; ?></h1>

<p><?php printf(LANG_USERS_INVITES_COUNT, html_spellcount($profile['invites_count'], LANG_USERS_INVITES_SPELLCOUNT)); ?></p>

<?php
    $this->renderForm($form, $input, array(
        'action' => '',
        'method' => 'post',
        'toolbar' => false,
        'submit' => array(
            'title' => LANG_SEND
        )
    ), $errors);
