<?php

class formBillingSystem extends cmsForm {

    public function init($options) {

        return [
            'basic' => [
                'title'  => LANG_CP_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_enabled', [
                        'title' => LANG_BILLING_CP_SYSTEM_IS_ENABLED,
                        'hint'  => LANG_BILLING_CP_SYSTEM_IS_ENABLED_HINT
                    ]),
                    new fieldString('title', [
                        'title' => LANG_BILLING_CP_SYSTEM_TITLE,
                        'hint'  => LANG_BILLING_CP_SYSTEM_TITLE_HINT,
                        'rules' => [
                            ['required'],
                            ['max_length', 64]
                        ]
                    ]),
                    new fieldString('payment_url', [
                        'title' => LANG_BILLING_CP_SYSTEM_PAYMENT_URL,
                        'rules' => [
                            ['required'],
                            ['max_length', 255]
                        ]
                    ]),
                    new fieldNumber('rate', [
                        'title'  => LANG_BILLING_CP_SYSTEM_RATE,
                        'prefix' => sprintf(LANG_BILLING_CP_SYSTEM_RATE_PREFIX, $options['currency_real']),
                        'units'  => html_spellcount_only(10, $options['currency']),
                        'options' => [
                            'is_abs' => true
                        ],
                        'rules'  => [
                            ['required'],
                            ['number'],
                            ['min', 0.0001],
                            ['max', 9999.9999]
                        ]
                    ])
                ]
            ]
        ];
    }

}
