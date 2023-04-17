<?php

function grid_menu_items($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', ['menu_items']),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title'    => LANG_CP_MENU_ITEM_TITLE,
            'width'    => 250,
            'href'     => href_to($controller->name, 'menu', ['item_edit', '{id}']),
            'editable' => []
        ],
        'url' => [
            'title'    => LANG_CP_MENU_ITEM_URL,
            'class'    => 'd-none d-lg-table-cell',
            'editable' => []
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to('admin', 'toggle_item', ['{id}', 'menu_items', 'is_enabled']),
            'width'       => 80
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'menu', ['item_edit', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'menu', ['item_delete', '{id}']),
            'confirm' => LANG_CP_MENU_ITEM_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
