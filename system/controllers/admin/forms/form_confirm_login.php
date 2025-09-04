<?php

class formAdminConfirmLogin extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type' => 'fieldset',
                'title' => LANG_PASSWORD,
                'childs' => [
                    new fieldString('password', [
                        'is_password' => true,
                        'rules' => [
                            ['required'],
                            ['min_length', 6]
                        ]
                    ])
                ]
            ]
        ];
    }

}
