<?php

class formSubscriptionsGuest extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('email', [
                        'title' => LANG_EMAIL,
                        'options'=>[
                            'max_length'=> 100
                        ],
                        'rules' => [
                            ['required'],
                            ['email']
                        ]
                    ]),
                    new fieldString('name', [
                        'title' => LANG_NAME,
                        'options'=>[
                            'max_length'=> 50
                        ],
                        'rules' => [
                            ['required'],
                            ['regexp', '/^([0-9a-zа-яёй\.\@\,\ \-]+)$/ui']
                        ]
                    ])
                ]
            ]
        ];
    }

}
