<?php

function grid_fields($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', '{users}_fields'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id'  => [
            'title' => 'id',
            'width' => 30
        ],
        'title' => [
            'title'    => LANG_CP_FIELD_TITLE,
            'href'     => href_to($controller->root_url, 'fields_edit', ['users', '{id}']),
            'editable' => []
        ],
        'fieldset' => [
            'title'   => LANG_CP_FIELD_FIELDSET,
            'class'   => 'd-none d-lg-table-cell',
            'width'   => 150,
            'handler' => function ($value, $row) {
                return $value ? $value : '&mdash;';
            },
            'filter' => 'exact',
            'filter_select' => [
                'items' => function($name) {
                    $fieldsets = cmsCore::getModel('content')->setTablePrefix('')->getContentFieldsets('{users}');
                    $items = ['' => LANG_ALL];
                    foreach($fieldsets as $fieldset) { $items[$fieldset] = $fieldset; }
                    return $items;
                }
            ]
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', '{users}_fields', 'is_enabled']),
            'width'       => 80
        ],
        'is_in_list' => [
            'title'       => LANG_CP_FIELD_IN_LIST_SHORT,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', '{users}_fields', 'is_in_list']),
            'width'       => 60
        ],
        'is_in_item' => [
            'title'       => LANG_CP_FIELD_IN_ITEM_SHORT,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', '{users}_fields', 'is_in_item']),
            'width'       => 60
        ],
        'name' => [
            'title' => LANG_SYSTEM_NAME,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150
        ],
        'type' => [
            'title' => LANG_CP_FIELD_TYPE,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150,
            'handler' => function ($value, $row) {
                return $row['handler_title'];
            },
            'filter' => 'exact',
            'filter_select' => [
                'items' => function($name) {
                    return ['' => LANG_ALL] + cmsForm::getAvailableFormFields('only_public', 'users');
                }
            ]
        ]
    ];

    $actions = [
        [
            'title' => LANG_COPY,
            'class' => 'copy',
            'href'  => href_to($controller->root_url, 'fields_add', ['users', '{id}', 1]),
            'handler' => function ($row) {
                return !$row['is_system'] && !$row['is_fixed'];
            }
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'fields_edit', ['users', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'fields_delete', ['{id}']),
            'confirm' => LANG_CP_FIELD_DELETE_CONFIRM,
            'handler' => function ($row) {
                return !$row['is_fixed'];
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
