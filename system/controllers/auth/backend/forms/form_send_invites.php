<?php
class formAuthSendInvites extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_AUTH_INVITES_STARGET,
                'childs' => [
                    new fieldListGroups('groups', [
                        'title' => LANG_AUTH_INVITES_SGROUP,
                        'show_all' => false,
                        'rules' => [
                            [function ($controller, $data, $value) {

                                if (empty($data['user_email']) && !$value) {
                                    return ERR_VALIDATE_REQUIRED;
                                }

                                return true;
                            }]
                        ]
                    ]),
                    new fieldString('user_email', [
                        'title' => LANG_AUTH_INVITES_SUSER,
                        'autocomplete' => ['url' => href_to('admin', 'users', 'autocomplete')],
                        'rules' => [
                            ['email']
                        ]
                    ]),
                    new fieldNumber('invites_qty', [
                        'title' => LANG_AUTH_INVITES_QTY,
                        'default' => 3,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_AUTH_INVITES_SPARAMS,
                'childs' => [
                    new fieldNumber('invites_min_karma', [
                        'title' => LANG_AUTH_INVITES_KARMA,
                        'default' => 0
                    ]),
                    new fieldNumber('invites_min_rating', [
                        'title' => LANG_AUTH_INVITES_RATING,
                        'default' => 0
                    ]),
                    new fieldNumber('invites_min_days', [
                        'title' => LANG_AUTH_INVITES_DATE,
                        'units' => LANG_DAY10
                    ])
                ]
            ]
        ];
    }

}
