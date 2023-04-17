<?php

function grid_ctype_props($controller, $drag_save_url){

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => $drag_save_url,
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title' => LANG_CP_FIELD_TITLE,
            'href'  => href_to($controller->name, 'ctypes', ['props_edit', '{ctype_id}', '{prop_id}'])
        ],
        'fieldset' => [
            'title'   => LANG_CP_FIELD_FIELDSET,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($value, $row) {
                return $value ? $value : '&mdash;';
            }
        ],
        'is_in_filter' => [
            'title'       => LANG_FILTER,
            'class'       => 'd-none d-lg-table-cell',
            'flag'        => true,
            'flag_toggle' => href_to($controller->name, 'ctypes', ['props_toggle', '{ctype_id}', '{prop_id}']),
            'width'       => 60
        ],
        'type' => [
            'title'   => LANG_CP_FIELD_TYPE,
            'width'   => 150,
            'handler' => function ($value, $row) {
                return constant('LANG_PARSER_' . mb_strtoupper($value));
            }
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'ctypes', ['props_edit', '{ctype_id}', '{prop_id}'])
        ],
        [
            'title'   => LANG_CP_PROPS_UNBIND,
            'class'   => 'unbind',
            'href'    => href_to($controller->name, 'ctypes', ['props_unbind', '{ctype_id}', '{prop_id}', '{cat_id}']),
            'confirm' => LANG_CP_PROPS_UNBIND_CONFIRM
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', ['props_delete', '{ctype_id}', '{prop_id}']),
            'confirm' => LANG_CP_PROPS_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
