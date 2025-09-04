<?php
class formAdminMailCheck extends cmsForm {

    public function init() {

        return [
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('email', [
                        'title' => LANG_MAILCHECK_TO,
                        'rules' => [
                            ['required'],
                            ['email']
                        ]
                    ]),
                    new fieldString('subject', [
                        'title' => LANG_MAILCHECK_SUBJECT,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldText('body', [
                        'title' => LANG_MAILCHECK_BODY,
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
