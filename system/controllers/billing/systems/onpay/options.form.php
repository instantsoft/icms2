<?php

class formOnpaySystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:merchant_login', [
                        'title' => LANG_BILLING_SYSTEM_ONPAY_LOGIN,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:password1', [
                        'title'       => LANG_BILLING_SYSTEM_ONPAY_KEY,
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
