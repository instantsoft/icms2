<?php

class formYakassaSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:shop_id', [
                        'title' => LANG_BILLING_SYSTEM_YAKASSA_SHOP_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:scid', [
                        'title' => LANG_BILLING_SYSTEM_YAKASSA_SCID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:key', [
                        'title' => LANG_BILLING_SYSTEM_YAKASSA_KEY,
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
