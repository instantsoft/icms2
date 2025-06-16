<?php

function grid_payouts($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => true,
        'is_draggable'  => true,
        'is_selectable' => false,
        'order_by'      => 'id',
        'order_to'      => 'desc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title' => LANG_BILLING_CP_PO_GRID_TITLE,
            'href'  => href_to($controller->root_url, 'payouts', ['edit', '{id}'])
        ],
        'date_last' => [
            'title'   => LANG_BILLING_CP_PO_GRID_DATE,
            'handler' => function ($value) {
                return $value ? html_date_time($value) : '&mdash;';
            }
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'billing_payouts', 'is_enabled']),
            'width'       => 60
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'payouts', ['edit', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'confirm' => LANG_BILLING_CP_PO_DELETE_CONFIRM,
            'href'    => href_to($controller->root_url, 'payouts', ['delete', '{id}'])
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
