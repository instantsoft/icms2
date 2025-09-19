<?php

class formAuthReset extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('password1', [
                        'title'       => LANG_NEW_PASS,
                        'is_password' => true,
                        'options'     => [
                            'min_length' => 6,
                            'max_length' => 72
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('password2', [
                        'title'       => LANG_RETYPE_NEW_PASS,
                        'is_password' => true,
                        'options'     => [
                            'min_length' => 6,
                            'max_length' => 72
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
