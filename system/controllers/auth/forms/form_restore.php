<?php

class formAuthRestore extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_EMAIL,
                'childs' => [
                    new fieldString('email', [
                        'rules' => [
                            ['required'],
                            ['email'],
                            [function($controller, $data, $value) {

                                $users_model = cmsCore::getModel('users');

                                $user = $users_model->getUserByEmail($value);

                                if (!$user) {

                                    return LANG_EMAIL_NOT_FOUND;

                                } elseif ($user['is_locked']) {

                                    return LANG_RESTORE_BLOCK . ($user['lock_reason'] ? '. ' . $user['lock_reason'] : '');

                                } elseif ($user['is_deleted']) {

                                    return LANG_RESTORE_IS_DELETED;

                                } elseif ($user['pass_token']) {

                                    if ((strtotime($user['date_token']) + (24 * 3600)) >= time()) {
                                        return LANG_RESTORE_TOKEN_IS_SEND;
                                    }
                                }

                                return true;
                            }]
                        ]
                    ])
                ]
            ]
        ];
    }

}
