<?php

function grid_ctypes($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_draggable'  => true,
        'show_id'       => false,
        'drag_save_url' => href_to('admin', 'reorder', ['content_types']),
        'order_by'      => 'ordering',
        'order_to'      => 'asc'
    ];

    $columns = [
        'id' => [
            'title'  => 'id',
            'width'  => 30
        ],
        'title' => [
            'title'  => LANG_TITLE,
            'width'  => 150,
            'href'   => href_to($controller->name, 'ctypes', ['edit', '{id}']),
            'filter' => 'like'
        ],
        'name' => [
            'title'  => LANG_SYSTEM_NAME,
            'class'  => 'd-none d-sm-table-cell',
            'width'  => 150,
            'filter' => 'like'
        ],
        'url_pattern' => [
            'title' => LANG_CP_URL_PATTERN,
            'class' => 'd-none d-lg-table-cell',
            'width' => 200
        ],
        'is_cats' => [
            'title'   => LANG_CATEGORIES,
            'class'   => 'd-none d-lg-table-cell',
            'width'   => 90,
            'handler' => function ($value, $ctype) {
                return html_bool_span(($value ? LANG_YES : LANG_NO), $value, ['badge badge-dark', 'positive badge badge-success']);
            }
        ],
        'is_folders' => [
            'title'   => LANG_CP_FOLDERS,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($value, $ctype) {
                return html_bool_span(($value ? LANG_YES : LANG_NO), $value, ['badge badge-dark', 'positive badge badge-success']);
            }
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'class'       => 'd-none d-sm-table-cell',
            'flag'        => true,
            'flag_toggle' => href_to($controller->name, 'toggle_item', ['{id}', 'content_types', 'is_enabled']),
            'width'       => 80
        ]
    ];

    $actions = [
        [
            'title'   => LANG_VIEW,
            'class'   => 'view',
            'href'    => href_to('{name}'),
            'handler' => function ($row) {
                return !empty($row['options']['list_on']);
            }
        ],
        [
            'title' => LANG_OPTIONS,
            'class' => 'config',
            'href'  => href_to($controller->name, 'ctypes', ['edit', '{id}'])
        ],
        [
            'title' => LANG_CP_CTYPE_LABELS,
            'class' => 'labels',
            'href'  => href_to($controller->name, 'ctypes', ['labels', '{id}'])
        ],
        [
            'title' => LANG_CP_CTYPE_FIELDS,
            'class' => 'fields',
            'href'  => href_to($controller->name, 'ctypes', ['fields', '{id}'])
        ],
        [
            'title' => LANG_CP_CTYPE_PERMISSIONS,
            'class' => 'permissions',
            'href'  => href_to($controller->name, 'ctypes', ['perms', '{id}'])
        ],
        [
            'title' => LANG_CP_CTYPE_DATASETS,
            'class' => 'filter',
            'href'  => href_to($controller->name, 'ctypes', ['datasets', '{id}'])
        ],
        [
            'title' => LANG_MODERATORS,
            'class' => 'user',
            'href'  => href_to($controller->name, 'ctypes', ['moderators', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', ['delete', '{id}']),
            'confirm' => LANG_CP_CTYPE_DELETE_CONFIRM,
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
