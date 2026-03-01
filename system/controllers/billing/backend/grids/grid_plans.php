<?php

function grid_plans($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => true,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', 'billing_plans'),
        'is_selectable' => false,
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title' => LANG_BILLING_PLAN,
            'href'  => href_to($controller->root_url, 'plans', ['edit', '{id}'])
        ],
        'prices' => [
            'title' => LANG_BILLING_PLAN_PRICE,
            'handler' => function ($value, $row) use($controller) {

                $from = '';

                $prices = cmsModel::yamlToArray($value);

                $price = [
                    'amount'  => 0,
                    'int_str' => LANG_MONTH1
                ];

                if ($prices) {

                    $price = reset($prices);

                    $price['amount'] = $controller->model->getDepositSumm($price['amount']);
                    $price['int_str'] = string_lang($price['int'] . '1');

                    if (count($prices) > 1) {
                        $from = LANG_FROM . ' ';
                    }
                }

                return $from . '' . $price['amount'] . ' ' . $controller->options['cur_real_symb'] . ' / ' . $price['int_str'];;
            }
        ],
        'users' => [
            'title' => LANG_BILLING_PLAN_USERS,
            'width' => 100,
            'href'  => href_to($controller->root_url, 'plans', ['users', '{id}']),
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'billing_plans', 'is_enabled']),
            'width'       => 60
        ]
    ];

    $actions = [
        [
            'title'   => LANG_BILLING_PLAN_RUN_FREE,
            'confirm' => LANG_BILLING_PLAN_RUN_FREE_CONFIRM,
            'icon'    => 'user-check',
            'href'    => href_to($controller->root_url, 'plans_free_all', ['{id}']),
            'handler' => function ($item) {

                $prices = cmsModel::yamlToArray($item['prices']);

                return empty($prices);
            }
        ],
        [
            'title' => LANG_COPY,
            'icon'  => 'copy',
            'href'  => href_to($controller->root_url, 'plans_add', ['{id}', 1]),
        ],
        [
            'title' => LANG_EDIT,
            'icon'  => 'pen',
            'href'  => href_to($controller->root_url, 'plans', ['edit', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'text-danger',
            'icon'    => 'times-circle',
            'confirm' => LANG_BILLING_PLAN_DELETE_CONFIRM,
            'href'    => href_to($controller->root_url, 'plans', ['delete', '{id}'])
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
