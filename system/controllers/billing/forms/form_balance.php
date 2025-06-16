<?php

class formBillingBalance extends cmsForm {

    public function init() {

        return [
            'amount' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('amount', [
                        'title' => LANG_BILLING_CP_BAL_AMOUNT,
                        'hint'  => LANG_BILLING_CP_BAL_HINT,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('description', [
                        'title' => LANG_BILLING_CP_BAL_DESCRIPTION,
                        'hint'  => LANG_BILLING_CP_BAL_DESCRIPTION_HINT,
                        'rules' => [
                            ['max_length', 255]
                        ]
                    ])
                ]
            ]
        ];
    }

}
