<?php

function grid_ctype_props($controller, $drag_save_url, $ctype_name){

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
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
            'title'  => LANG_CP_FIELD_TITLE,
            'href'   => href_to($controller->name, 'ctypes', ['props_edit', '{ctype_id}', '{prop_id}']),
            'filter_by' => 'p.title',
            'filter' => 'like'
        ],
        'fieldset' => [
            'title'   => LANG_CP_FIELD_FIELDSET,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($value, $row) {
                return $value ? $value : '&mdash;';
            },
            'filter_by' => 'p.fieldset',
            'filter' => 'exact',
            'filter_select' => array(
                'items' => function($name) use($ctype_name) {
                    $fieldsets = cmsCore::getModel('content')->getContentFieldsets($ctype_name, '_props');
                    $items = ['' => LANG_ALL];
                    foreach($fieldsets as $fieldset) { $items[$fieldset] = $fieldset; }
                    return $items;
                }
            )
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
                return $row['handler_title'];
            },
            'filter_by' => 'p.type',
            'filter' => 'exact',
            'filter_select' => array(
                'items' => function($name) {
                    return ['' => LANG_ALL] + modelBackendContent::PROP_FIELDS;
                }
            )
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
