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
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'plans', ['edit', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
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
