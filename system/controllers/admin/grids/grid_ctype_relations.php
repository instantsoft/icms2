<?php

function grid_ctype_relations($controller) {

    cmsCore::loadAllControllersLanguages();

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', ['content_relations']),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title' => LANG_CP_RELATION_TITLE,
            'href'  => href_to($controller->name, 'ctypes', ['relations_edit', '{ctype_id}', '{id}']),
        ],
        'layout' => [
            'title'   => LANG_CP_RELATION_LAYOUT_TYPE,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($value, $row) {
                return constant('LANG_CP_RELATION_LAYOUT_' . strtoupper($value));
            }
        ],
        'target_controller' => [
            'title'   => LANG_EVENTS_LISTENER,
            'width'   => 100,
            'handler' => function ($value, $row) {
                return string_lang('LANG_' . strtoupper($value) . '_CONTROLLER');
            }
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'ctypes', ['relations_edit', '{ctype_id}', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', ['relations_delete', '{id}']),
            'confirm' => LANG_CP_RELATION_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
