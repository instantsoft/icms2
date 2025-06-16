<?php

class formInterkassaSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:ik_co_id', [
                        'title' => LANG_BILLING_SYSTEM_INTERKASSA_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:ik_secret_key', [
                        'title'       => LANG_BILLING_SYSTEM_INTERKASSA_KEY,
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
