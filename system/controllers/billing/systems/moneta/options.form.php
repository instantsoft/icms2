<?php

class formMonetaSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:mnt_id', [
                        'title' => LANG_BILLING_SYSTEM_MONETA_MERCHANT,
                        'hint'  => LANG_BILLING_SYSTEM_MONETA_MERCHANT_HINT,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:key', [
                        'title' => LANG_BILLING_SYSTEM_MONETA_KEY,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:currency', [
                        'title'   => LANG_BILLING_SYSTEM_MONETA_CURRENCY,
                        'default' => 'RUB',
                        'rules'   => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
