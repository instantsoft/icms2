<?php

$this->setPageTitle(LANG_AUTH_SEND_INVITES);
$this->addBreadcrumb(LANG_AUTH_SEND_INVITES);

$this->renderForm($form, $data, [
    'action'  => '',
    'method'  => 'post',
    'buttons' => [
        [
            'title'      => LANG_AUTH_REVOKE_INVITES,
            'name'       => 'revoke_invites',
            'attributes' => [
                'type' => 'submit'
            ]
        ]
    ],
    'submit' => [
        'title' => LANG_SUBMIT
    ]
], $errors);
