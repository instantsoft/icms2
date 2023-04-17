<?php

function grid_tabs($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', '{users}_tabs'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id',
            'width' => 30
        ],
        'title' => [
            'title'    => LANG_CP_TAB_TITLE,
            'href'     => href_to($controller->root_url, 'tabs_edit', ['{id}']),
            'editable' => []
        ],
        'name' => [
            'title' => LANG_SYSTEM_NAME,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150
        ],
        'is_active' => [
            'title'       => LANG_SHOW,
            'flag'        => true,
            'width'       => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', '{users}_tabs', 'is_active'])
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'tabs_edit', ['{id}'])
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
