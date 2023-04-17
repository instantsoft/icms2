<?php

function grid_countries($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', 'geo_countries'),
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
            'href'     => href_to($controller->root_url, 'regions', ['{id}']),
            'filter'   => 'like',
            'editable' => [
                'rules' => [
                    ['required'],
                    ['max_length', 128]
                ]
            ]
        ],
        'alpha2' => [
            'title'    => LANG_GEO_ALPHA2,
            'class'    => 'd-none d-sm-table-cell',
            'width'    => 250,
            'filter'   => 'like',
            'editable' => [
                'rules' => [
                    ['required'],
                    ['max_length', 2]
                ]
            ]
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'geo_countries', 'is_enabled']),
            'width'       => 80
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'country', ['{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', ['country', '{id}']),
            'confirm' => LANG_GEO_DELETE_COUNTRY
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
