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
        'wysiwyg_name' => [
            'title'   => LANG_PARSER_HTML_EDITOR,
            'width'   => 150,
            'filter'  => 'exact',
            'filter_select' => array(
                'items' => function($name){
                    $items = ['' => LANG_ALL];
                    $editors = cmsCore::getWysiwygs();
                    foreach($editors as $editor){
                        $items[$editor] = $editor;
                    }
                    return $items;
                }
            ),
            'handler' => function ($v, $row){
                return ucfirst($v);
            }
        ],
        'title' => [
            'title'  => LANG_WW_PRESET_TITLE,
            'filter' => 'like',
            'href'   => href_to($controller->root_url, 'presets_edit', ['{id}']),
            'editable' => []
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
            'confirm' => LANG_WW_PRESET_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
