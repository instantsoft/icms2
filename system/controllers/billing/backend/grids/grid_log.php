<?php

function grid_log($controller, $model) {

    $options = [
        'is_sortable'   => true,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'is_selectable' => false,
        'order_by'      => 'id',
        'order_to'      => 'desc',
        'show_id'       => true
    ];

    $columns = [
        'id' => [
            'title'  => 'id',
            'class_handler' => function($row) {
                if ($row['status'] == modelBilling::STATUS_CANCELED) {
                    return 'bg-danger';
                }
            }
        ],
        'date_created' => [
            'title'   => LANG_BILLING_LOG_DATE,
            'width'   => 150,
            'filter'  => 'date',
            'handler' => function ($value) {
                return html_date_time($value);
            }
        ],
        'user_nickname' => [
            'title'     => LANG_USER,
            'href'      => href_to('users', '{user_id}'),
            'width'     => 150,
            'order_by'  => 'u.nickname',
            'filter'    => 'like',
            'filter_by' => 'u.nickname'
        ],
        'description' => [
            'title'  => LANG_BILLING_LOG_DESCRIPTION,
            'filter' => 'like'
        ],
        'amount' => [
            'title'   => LANG_BILLING_LOG_AMOUNT,
            'filter'  => 'range',
            'handler' => function ($value) {
                return $value ? '<span class="' . html_signed_class($value) . '">' . html_signed_num(nf($value, 2, '')) . '</span>' : '&mdash;';
            }
        ],
        'summ' => [
            'title'   => LANG_BILLING_LOG_SUMM,
            'filter'  => 'range',
            'handler' => function ($value) use($controller) {
                return $value ? nf($value, 2, '').' '.$controller->options['cur_real_symb'] : '&mdash;';
            }
        ],
        'system_id' => [
            'title'   => LANG_BILLING_DEPOSIT_SYSTEM,
            'handler' => function ($value, $row) {
                return $value ? $row['system_title'] : '&mdash;';
            },
            'filter' => 'exact',
            'filter_select' => [
                'items' => function($name) use($controller) {

                    $items = [];

                    $systems = $controller->model->getPaymentSystems(0);

                    foreach ($systems as $system) {
                        $items[$system['id']] = $system['title'];
                    }

                    return ['' => LANG_ALL] + $items;
                }
            ]
        ]
    ];

    $actions = [
        [
            'title'   => LANG_BILLING_LOG_REFUND,
            'icon'    => 'undo',
            'confirm' => LANG_BILLING_LOG_REFUND_CONFIRM,
            'href'    => href_to($controller->root_url, 'log', ['refund', '{id}']),
            'handler' => function ($log) use($model) {
                if ($log['status'] != modelBilling::STATUS_DONE) {
                    return false;
                }
                // Пополнение с платёжной системы
                if ($log['system_id']) {
                    return false;
                }
                // Подписки
                if ($log['plan_id'] && $log['user_plan_id']) {

                    $last_plan_log = $model->getLastUserPlanOperation($log['user_id']);

                    if (!$last_plan_log) {
                        return false;
                    }

                    return $last_plan_log['id'] == $log['id'];
                }

                return !empty($log['sender_id']);
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
