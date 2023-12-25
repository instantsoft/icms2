<?php

function grid_presets($controller){

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => false,
        'is_draggable'  => false,
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id',
            'width' => 30
        ],
        'title' => [
            'title'  => LANG_TYP_PRESET_TITLE,
            'filter' => 'like',
            'href'   => href_to($controller->root_url, 'presets_edit', ['{id}'])
        ]
    ];

    $actions = [
        [
            'title' => LANG_COPY,
            'class' => 'copy',
            'href'  => href_to($controller->root_url, 'presets_add', ['{id}', 1]),
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->root_url, 'presets_edit', ['{id}']),
        ],
        [
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->root_url, 'presets_delete', ['{id}']),
            'confirm' => LANG_TYP_PRESET_DELETE_CONFIRM,
            'handler' => function ($row) {
                return $row['id'] != 1;
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
