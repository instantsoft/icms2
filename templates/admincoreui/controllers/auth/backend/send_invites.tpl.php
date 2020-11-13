<?php

    $this->setPageTitle(LANG_AUTH_SEND_INVITES);
    $this->addBreadcrumb(LANG_AUTH_SEND_INVITES);

    $this->renderForm($form, $data, array(
        'action' => '',
        'method' => 'post',
        'buttons' => array(
            array(
                'title' => LANG_AUTH_REVOKE_INVITES,
                'name' => 'revoke_invites',
                'attributes' => array(
                    'type' => 'submit'
                )
            )
        ),
        'submit' => array(
            'title' => LANG_SUBMIT
        )
    ), $errors);
