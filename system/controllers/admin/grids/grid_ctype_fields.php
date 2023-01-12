<?php

function grid_ctype_fields($controller, $ctype_name) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => $controller->cms_template->href_to('ctypes', ['fields_reorder', $ctype_name]),
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
            'title' => LANG_CP_FIELD_TITLE,
            'href'  => href_to($controller->name, 'ctypes', ['fields_edit', '{ctype_id}', '{id}']),
            'filter' => 'like'
        ],
        'type' => [
            'title' => LANG_CP_FIELD_TYPE,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150,
            'handler' => function ($value, $row) {
                return $row['handler_title'];
            },
            'filter' => 'exact',
            'filter_select' => array(
                'items' => function($name) use($ctype_name) {
                    return ['' => LANG_ALL] + cmsForm::getAvailableFormFields('only_public', 'content');
                }
            )
        ],
        'fieldset' => [
            'title'   => LANG_CP_FIELD_FIELDSET,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($value, $row) {
                return $value ? $value : '&mdash;';
            },
            'filter' => 'exact',
            'filter_select' => array(
                'items' => function($name) use($ctype_name) {
                    $fieldsets = cmsCore::getModel('content')->getContentFieldsets($ctype_name);
                    $items = ['' => LANG_ALL];
                    foreach($fieldsets as $fieldset) { $items[$fieldset] = $fieldset; }
                    return $items;
                }
            )
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->name, 'ctypes', ['fields_toggle', 'enable', '{ctype_id}', '{id}']),
            'width'       => 80
        ],
        'is_in_list' => [
            'title'        => LANG_CP_FIELD_IN_LIST_SHORT,
            'class'        => 'd-none d-md-table-cell',
            'flag'         => true,
            'flag_toggle'  => href_to($controller->name, 'ctypes', ['fields_toggle', 'list', '{ctype_id}', '{id}']),
            'width'        => 60,
            'flag_handler' => function ($value, $row) {
                if (!empty($row['options']['context_list']) && array_search('0', $row['options']['context_list']) === false) {
                    return -1;
                }
                return $value;
            }
        ],
        'is_in_item' => [
            'title'       => LANG_CP_FIELD_IN_ITEM_SHORT,
            'class'       => 'd-none d-md-table-cell',
            'flag'        => true,
            'flag_toggle' => href_to($controller->name, 'ctypes', ['fields_toggle', 'item', '{ctype_id}', '{id}']),
            'width'       => 60
        ],
        'name' => [
            'title' => LANG_SYSTEM_NAME,
            'class' => 'd-none d-lg-table-cell',
            'width' => 120,
            'filter' => 'like'
        ]
    ];

    $actions = [
        [
            'title' => LANG_COPY,
            'class' => 'copy',
            'href'  => href_to($controller->name, 'ctypes', ['fields_add', '{ctype_id}', '{id}', 1]),
            'handler' => function ($row) {
                return !$row['is_system'] && !$row['is_fixed'];
            }
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'ctypes', ['fields_edit', '{ctype_id}', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', ['fields_delete', '{ctype_id}', '{id}']),
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
