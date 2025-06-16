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
                    new fieldString('options:password1', [
                        'title'       => LANG_BILLING_SYSTEM_ROBOKASSA_P1,
                        'is_password' => true,
                        'rules'       => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:password2', [
                        'title'       => LANG_BILLING_SYSTEM_ROBOKASSA_P2,
                        'is_password' => true,
                        'rules'       => [
                            ['required']
                        ]
                    ]),
                    new fieldCheckbox('options:fiscal_on', [
                        'title' => 'Включить отправку фискального чека',
                    ]),
                    new fieldList('options:fiscal_sno', [
                        'title'   => 'Система налогообложения',
                        'items'   => [
                            'osn'                => 'общая СН',
                            'usn_income'         => 'упрощенная СН (доходы)',
                            'usn_income_outcome' => 'упрощенная СН (доходы минус расходы)',
                            'envd'               => 'единый налог на вмененный доход',
                            'esn'                => 'единый сельскохозяйственный налог',
                            'patent'             => 'патентная СН',
                        ],
                        'default' => 'osn'
                    ]),
                    new fieldList('options:fiscal_method', [
                        'title'   => 'Признак способа расчета',
                        'items'   => [
                            'full_prepayment' => 'предоплата 100%',
                            'prepayment'      => 'предоплата',
                            'advance'         => 'аванс',
                            'full_payment'    => 'полный расчёт',
                            'partial_payment' => 'частичный расчёт и кредит',
                            'credit'          => 'передача в кредит',
                            'credit_payment'  => 'оплата кредита',
                        ],
                        'default' => 'full_payment'
                    ]),
                    new fieldList('options:fiscal_object', [
                        'title'   => 'Признак предмета расчёта',
                        'items'   => [
                            'commodity'             => 'товар',
                            'excise'                => 'подакцизный товар',
                            'job'                   => 'работа',
                            'service'               => 'услуга',
                            'gambling_bet'          => 'ставка азартной игры',
                            'gambling_prize'        => 'выигрыш азартной игры',
                            'lottery'               => 'лотерейный билет',
                            'lottery_prize'         => 'выигрыш лотереи',
                            'intellectual_activity' => 'интеллектуальная деятельность',
                            'payment'               => 'платеж',
                            'agent_commission'      => 'агентское вознаграждение',
                            'composite'             => 'составной предмет расчета',
                            'another'               => 'иной предмет расчета',
                            'property_right'        => 'имущественное право',
                            'non-operating_gain'    => 'внереализационный доход',
                            'insurance_premium'     => 'страховые взносы',
                            'sales_tax'             => 'торговый сбор',
                            'resort_fee'            => 'курортный сбор',
                        ],
                        'default' => 'service'
                    ]),
                    new fieldString('options:fiscal_name', [
                        'title' => 'Название товара в чеке',
                        'hint'  => 'Если не указано, будет использоваться стандартное описание пополнения баланса'
                    ]),
                    new fieldList('options:fiscal_tax', [
                        'title'   => 'Налоговая ставка в ККТ',
                        'items'   => [
                            'none'   => 'без НДС',
                            'vat0'   => 'НДС по ставке 0%',
                            'vat10'  => 'НДС чека по ставке 10%',
                            'vat110' => 'НДС чека по расчетной ставке 10/110',
                            'vat20'  => 'НДС чека по ставке 20%',
                            'vat120' => 'НДС чека по расчетной ставке 20/120',
                        ],
                        'default' => 'none'
                    ])
                ]
            ]
        ];
    }

}
