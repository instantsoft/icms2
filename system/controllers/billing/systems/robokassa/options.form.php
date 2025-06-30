<?php

class formRobokassaSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:merchant_login', [
                        'title' => LANG_BILLING_SYSTEM_ROBOKASSA_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('options:test_mode', [
                        'title' => LANG_BILLING_SYSTEM_ROBOKASSA_MODE,
                        'items' => [
                            0 => LANG_BILLING_SYSTEM_ROBOKASSA_MODE0,
                            1 => LANG_BILLING_SYSTEM_ROBOKASSA_MODE1
                        ]
                    ]),
                    new fieldString('options:password1', [
                        'title'       => LANG_BILLING_SYSTEM_ROBOKASSA_P1,
                        'is_password' => true,
                        'visible_depend' => ['options:test_mode' => ['show' => ['0']]]
                    ]),
                    new fieldString('options:password2', [
                        'title'       => LANG_BILLING_SYSTEM_ROBOKASSA_P2,
                        'is_password' => true,
                        'visible_depend' => ['options:test_mode' => ['show' => ['0']]]
                    ]),
                    new fieldString('options:password1_test', [
                        'title'       => LANG_BILLING_SYSTEM_ROBOKASSA_P1,
                        'is_password' => true,
                        'visible_depend' => ['options:test_mode' => ['show' => ['1']]]
                    ]),
                    new fieldString('options:password2_test', [
                        'title'       => LANG_BILLING_SYSTEM_ROBOKASSA_P2,
                        'is_password' => true,
                        'visible_depend' => ['options:test_mode' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:fiscal_on', [
                        'title' => LANG_BILLING_FISCAL_ON
                    ]),
                    new fieldList('options:fiscal_sno', [
                        'title'   => LANG_BILLING_FISCAL_SNO,
                        'items'   => [
                            'osn'                => LANG_BILLING_FISCAL_SNO_OSN,
                            'usn_income'         => LANG_BILLING_FISCAL_SNO_USN_INCOME,
                            'usn_income_outcome' => LANG_BILLING_FISCAL_SNO_USN_INCOME_OUTCOME,
                            'esn'                => LANG_BILLING_FISCAL_SNO_ESN,
                            'patent'             => LANG_BILLING_FISCAL_SNO_PATENT
                        ],
                        'default' => 'osn',
                        'visible_depend' => ['options:fiscal_on' => ['show' => ['1']]]
                    ]),
                    new fieldList('options:fiscal_method', [
                        'title'   => LANG_BILLING_FISCAL_M,
                        'items'   => [
                            'full_prepayment' => LANG_BILLING_FISCAL_M_FULL_PREPAYMENT,
                            'prepayment'      => LANG_BILLING_FISCAL_M_PREPAYMENT,
                            'advance'         => LANG_BILLING_FISCAL_M_ADVANCE,
                            'full_payment'    => LANG_BILLING_FISCAL_M_FULL_PAYMENT,
                            'partial_payment' => LANG_BILLING_FISCAL_M_PARTIAL_PAYMENT,
                            'credit'          => LANG_BILLING_FISCAL_M_CREDIT,
                            'credit_payment'  => LANG_BILLING_FISCAL_M_CREDIT_PAYMENT
                        ],
                        'default' => 'full_payment',
                        'visible_depend' => ['options:fiscal_on' => ['show' => ['1']]]
                    ]),
                    new fieldList('options:fiscal_object', [
                        'title'   => LANG_BILLING_FISCAL_OBJ,
                        'items'   => [
                            'commodity'             => LANG_BILLING_FISCAL_OBJ_COMMODITY,
                            'excise'                => LANG_BILLING_FISCAL_OBJ_EXCISE,
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
                    new fieldString('options:fiscal_name', [
                        'title' => LANG_BILLING_FISCAL_NAME,
                        'hint'  => LANG_BILLING_FISCAL_NAME_HINT,
                        'visible_depend' => ['options:fiscal_on' => ['show' => ['1']]]
                    ]),
                    new fieldList('options:fiscal_tax', [
                        'title'   => LANG_BILLING_FISCAL_TAX,
                        'items'   => [
                            'none'   => LANG_BILLING_FISCAL_TAX_NO,
                            'vat0'   => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RATE, '0%'),
                            'vat10'  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_RATE, '10%'),
                            'vat110' => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '10/110'),
                            'vat20'  => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_RATE, '20%'),
                            'vat120' => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '20/120'),
                            'vat5'   => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RATE, '5%'),
                            'vat7'   => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RATE, '7%'),
                            'vat105' => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '5/105'),
                            'vat107' => sprintf(LANG_BILLING_FISCAL_TAX_VAT_RECEIPT_EST_RATE, '7/107')
                        ],
                        'default' => 'none',
                        'visible_depend' => ['options:fiscal_on' => ['show' => ['1']]]
                    ])
                ]
            ]
        ];
    }

}
