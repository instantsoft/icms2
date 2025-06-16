<?php

class formYandexSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:receiver', [
                        'title' => LANG_BILLING_SYSTEM_YANDEX_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:secret_key', [
                        'title'       => LANG_BILLING_SYSTEM_YANDEX_KEY,
                        'is_password' => false,
                        'rules'       => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
