<?php

class formW1SystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:merchant_id', [
                        'title' => LANG_BILLING_SYSTEM_W1_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:key', [
                        'title' => LANG_BILLING_SYSTEM_W1_KEY,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:currency_id', [
                        'title' => LANG_BILLING_SYSTEM_W1_CURR_ID,
                        'hint'  => LANG_BILLING_SYSTEM_W1_CURR_ID_HINT,
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
