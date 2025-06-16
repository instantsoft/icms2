<?php

class formLiqpaySystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:public_key', [
                        'title' => LANG_BILLING_SYSTEM_LIQPAY_PUBLIC_KEY,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:private_key', [
                        'title' => LANG_BILLING_SYSTEM_LIQPAY_PRIVATE_KEY,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('options:currency', [
                        'title'   => LANG_BILLING_SYSTEM_LIQPAY_CURRENCY,
                        'default' => 'RUB',
                        'items'   => [
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'RUB' => 'RUB',
                            'UAH' => 'UAH',
                            'BYN' => 'BYN',
                            'KZT' => 'KZT'
                        ]
                    ]),
                    new fieldList('options:action', [
                        'title'   => LANG_BILLING_SYSTEM_LIQPAY_ACTION,
                        'default' => 'pay',
                        'items'   => [
                            'pay'       => LANG_BILLING_SYSTEM_LIQPAY_ACTION_PAY,
                            'paydonate' => LANG_BILLING_SYSTEM_LIQPAY_ACTION_PAYDONATE
                        ]
                    ])
                ]
            ]
        ];
    }

}
