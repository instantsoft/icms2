<?php

function grid_ctype_filters($controller, $ctype = []) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => false,
        'order_by'      => 'id',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title' => LANG_TITLE,
            'href'  => href_to($controller->name, 'ctypes', ['filters_add', $ctype['id'], '{id}', 'edit']),
            'editable' => [
                'attributes' => ['placeholder' => '{title}'],
                'rules' => [
                    ['required'],
                    ['max_length', 100]
                ]
            ]
        ],
        'slug' => [
            'title' => LANG_SYSTEM_NAME,
            'width' => 350
        ]
    ];

    $actions = [
        [
            'title'  => LANG_VIEW,
            'class'  => 'view',
            'target' => '_blank',
            'href'   => href_to(((cmsConfig::get('ctype_default') && in_array($ctype['name'], cmsConfig::get('ctype_default'))) ? '' : $ctype['name']), '{slug}')
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'ctypes', ['filters_add', $ctype['id'], '{id}', 'edit'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', ['filters_delete', $ctype['id'], '{id}']),
            'confirm' => LANG_CP_FILTER_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
