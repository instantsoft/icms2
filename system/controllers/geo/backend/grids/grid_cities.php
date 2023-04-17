<?php

function grid_cities($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', 'geo_cities'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'ID'
        ],
        'name' => [
            'title'    => LANG_TITLE,
            'href'     => href_to($controller->root_url, 'city', ['{id}']),
            'filter'   => 'like',
            'editable' => [
                'rules' => [
                    ['required'],
                    ['max_length', 128]
                ]
            ]
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'geo_cities', 'is_enabled']),
            'width'       => 80
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'city', ['{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', ['city', '{id}']),
            'confirm' => LANG_GEO_DELETE_CITY
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
