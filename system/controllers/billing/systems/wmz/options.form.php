<?php

class formWmzSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:purse', [
                        'title' => LANG_BILLING_SYSTEM_WMZ_PURSE,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:secret_key', [
                        'title'       => LANG_BILLING_SYSTEM_WMZ_SECRET_KEY,
                        'is_password' => true
                    ]),
                    new fieldList('options:test_mode', [
                        'title' => LANG_BILLING_SYSTEM_WMZ_TEST_MODE,
                        'items' => [
                            0 => LANG_BILLING_SYSTEM_WMZ_TEST_MODE_0,
                            1 => LANG_BILLING_SYSTEM_WMZ_TEST_MODE_1,
                            2 => LANG_BILLING_SYSTEM_WMZ_TEST_MODE_2
                        ]
                    ])
                ]
            ]
        ];
    }

}
