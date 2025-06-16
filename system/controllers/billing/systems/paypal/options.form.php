<?php

class formPaypalSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:account', [
                        'title' => LANG_BILLING_SYSTEM_PAYPAL_ACCOUNT,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:currency', [
                        'title'   => LANG_BILLING_SYSTEM_PAYPAL_CURRENCY,
                        'default' => 'RUB',
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:client_id', [
                        'title' => LANG_BILLING_SYSTEM_PAYPAL_CLIENT_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:secret', [
                        'title' => LANG_BILLING_SYSTEM_PAYPAL_SECRET,
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
