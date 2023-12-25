<?php

class formMessagesPmailing extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_PM_PMAILING_GROUPS,
                'childs' => [
                    new fieldListGroups('groups', [
                        'show_all' => true
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldHtml('message_text', [
                        'title'   => LANG_MESSAGE,
                        'hint'    => LANG_PM_PMAILING_TYPOGRAPH,
                        'options' => ['editor' => 'ace'],
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldList('type', [
                        'title' => LANG_PM_PMAILING_TYPE,
                        'items' => [
                            'notify'  => LANG_PM_PMAILING_TYPE_NOTIFY,
                            'message' => LANG_PM_PMAILING_TYPE_MESSAGE,
                            'email'   => LANG_PM_PMAILING_TYPE_EMAIL
                        ]
                    ]),
                    new fieldString('sender_user_email', [
                        'title'        => LANG_PM_SENDER_USER_ID,
                        'hint'         => LANG_PM_SENDER_USER_ID_HINT,
                        'autocomplete' => ['url' => href_to('admin', 'users', 'autocomplete')],
                        'rules'        => [
                            ['email']
                        ]
                    ])
                ]
            ]
        ];
    }

}
