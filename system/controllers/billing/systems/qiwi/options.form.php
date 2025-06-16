<?php

class formQiwiSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldNumber('options:shop_id', [
                        'title' => LANG_BILLING_SYSTEM_QIWI_SHOP_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:api_id', [
                        'title' => LANG_BILLING_SYSTEM_QIWI_API_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:api_password', [
                        'title'       => LANG_BILLING_SYSTEM_QIWI_API_PASSWORD,
                        'is_password' => true,
                        'rules'       => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:password', [
                        'title'       => LANG_BILLING_SYSTEM_QIWI_PASSWORD,
                        'is_password' => true,
                        'rules'       => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
