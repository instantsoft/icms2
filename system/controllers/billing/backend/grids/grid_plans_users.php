<?php

function grid_plans_users($controller) {

    $options = [
        'is_sortable'   => true,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'is_selectable' => false,
        'order_by'      => 'date_until',
        'order_to'      => 'desc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'user_nickname' => [
            'title'     => LANG_USER,
            'href'      => href_to('users', '{user_id}'),
            'order_by'  => 'u.nickname',
            'filter'    => 'like',
            'filter_by' => 'u.nickname'
        ],
        'date_until' => [
            'title'   => LANG_BILLING_PLAN_DATE_UNTIL,
            'filter'  => 'date',
            'handler' => function ($value) {
                return html_date($value);
            }
        ],
        'is_paused' => [
            'title'   => LANG_BILLING_OUT_STATUS,
            'width'   => 150,
            'handler' => function ($value) {
                return html_bool_span(($value ? LANG_BILLING_PLAN_INACTIVE : LANG_BILLING_PLAN_ACTIVE), !$value);
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => false
    ];
}
