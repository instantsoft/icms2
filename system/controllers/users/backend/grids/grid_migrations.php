<?php

function grid_migrations($controller) {

    $options = [
        'show_id'   => false,
        'is_filter' => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title'    => LANG_USERS_MIG_TITLE,
            'href'     => href_to($controller->root_url, 'migrations_edit', ['{id}']),
            'editable' => []
        ],
        'passed_days' => [
            'title'   => LANG_USERS_MIG_PASSED_DAYS,
            'handler' => function ($val) {
                return $val ? $val : '&mdash;';
            },
            'width' => 80
        ],
        'rating' => [
            'title'   => LANG_RATING,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($val) {
                return $val ? $val : '&mdash;';
            },
            'width' => 80
        ],
        'karma' => [
            'title'   => LANG_KARMA,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($val) {
                return $val ? $val : '&mdash;';
            },
            'width' => 80
        ],
        'is_active' => [
            'title'       => LANG_ON,
            'flag'        => true,
            'width'       => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', '{users}_groups_migration', 'is_active'])
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'migrations_edit', ['{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'migrations_delete', ['{id}']),
            'confirm' => LANG_USERS_MIG_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
