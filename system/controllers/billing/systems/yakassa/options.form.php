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
                    new fieldString('options:key', [
                        'title' => LANG_BILLING_SYSTEM_YAKASSA_KEY,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldCheckbox('options:fiscal_on', [
                        'title' => LANG_BILLING_FISCAL_ON
                    ]),
                    new fieldList('options:fiscal_method', [
                        'title'   => LANG_BILLING_FISCAL_M,
                        'items'   => [
                            'full_prepayment' => LANG_BILLING_FISCAL_M_FULL_PREPAYMENT,
                            'full_payment'    => LANG_BILLING_FISCAL_M_FULL_PAYMENT
                        ],
                        'default' => 'full_payment',
                        'visible_depend' => ['options:fiscal_on' => ['show' => ['1']]]
                    ]),
                    new fieldList('options:fiscal_object', [
                        'title'   => LANG_BILLING_FISCAL_OBJ,
                        'items'   => [
                            'commodity'             => LANG_BILLING_FISCAL_OBJ_COMMODITY,
                            'job'                   => LANG_BILLING_FISCAL_OBJ_JOB,
                            'service'               => LANG_BILLING_FISCAL_OBJ_SERVICE,
                            'gambling_bet'          => LANG_BILLING_FISCAL_OBJ_GAMBLING_BET,
                            'gambling_prize'        => LANG_BILLING_FISCAL_OBJ_GAMBLING_PRIZE,
                            'lottery'               => LANG_BILLING_FISCAL_OBJ_LOTTERY,
                            'lottery_prize'         => LANG_BILLING_FISCAL_OBJ_LOTTERY_PRIZE,
                            'intellectual_activity' => LANG_BILLING_FISCAL_OBJ_INTELLECTUAL_ACTIVITY,
                            'payment'               => LANG_BILLING_FISCAL_OBJ_PAYMENT,
                            'agent_commission'      => LANG_BILLING_FISCAL_OBJ_AGENT_COMMISSION,
                            'composite'             => LANG_BILLING_FISCAL_OBJ_COMPOSITE,
                            'another'               => LANG_BILLING_FISCAL_OBJ_ANOTHER,
                            'property_right'        => LANG_BILLING_FISCAL_OBJ_PROPERTY_RIGHT,
                            'non-operating_gain'    => LANG_BILLING_FISCAL_OBJ_NON_OPERATING_GAIN,
                            'insurance_premium'     => LANG_BILLING_FISCAL_OBJ_INSURANCE_PREMIUM,
                            'sales_tax'             => LANG_BILLING_FISCAL_OBJ_SALES_TAX,
                            'resort_fee'            => LANG_BILLING_FISCAL_OBJ_RESORT_FEE
                        ],
                        'default' => 'service',
                        'visible_depend' => ['options:fiscal_on' => ['show' => ['1']]]
                    ]),
                    new fieldList('options:fiscal_tax', [
                        'title'   => LANG_BILLING_FISCAL_TAX,
                        'items'   => [
                            1  => LANG_BILLING_FISCAL_TAX_NO,
                            2  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RATE, '0%'),
                            3  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_RATE, '10%'),
                            4  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_RATE, '20%'),
                            5  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '10/110'),
                            6  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '20/120'),
                            7  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RATE, '5%'),
                            8  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RATE, '7%'),
                            9  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '5/105'),
                            10 => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '7/107')
                        ],
                        'default' => 1,
                        'visible_depend' => ['options:fiscal_on' => ['show' => ['1']]]
                    ])
                ]
            ]
        ];
    }

}
