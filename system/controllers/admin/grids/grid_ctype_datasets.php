<?php

function grid_ctype_datasets($controller, $edit_url) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', ['content_datasets']),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title'    => LANG_CP_DATASET_TITLE,
            'href'     => $edit_url,
            'editable' => [
                'rules' => [
                    ['required'],
                    ['max_length', 100]
                ]
            ]
        ],
        'max_count' => [
            'title'   => LANG_LIST_LIMIT,
            'class'   => 'd-none d-lg-table-cell',
            'width'   => 130,
            'handler' => function ($value, $row) {
                return $value ? $value : '&mdash;';
            }
        ],
        'name' => [
            'title' => LANG_SYSTEM_NAME,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150
        ],
        'is_visible' => [
            'title'       => LANG_PUBLICATION,
            'flag'        => true,
            'flag_toggle' => href_to('admin', 'toggle_item', ['{id}', 'content_datasets', 'is_visible']),
            'width'       => 90
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => $edit_url
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', ['datasets_delete', '{id}']),
            'confirm' => LANG_CP_DATASET_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
