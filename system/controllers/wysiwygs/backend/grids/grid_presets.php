<?php

function grid_presets($controller){

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => false,
        'show_id'       => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
        ),
        'wysiwyg_name' => array(
            'title' => LANG_PARSER_HTML_EDITOR,
            'width' => 150,
            'handler' => function ($v, $row){
                return ucfirst($v);
            }
        ),
        'title' => array(
            'title' => LANG_WW_PRESET_TITLE,
            'href' => href_to($controller->root_url, 'presets_edit', array('{id}')),
            'editable' => array(
                'table' => 'wysiwygs_presets'
            )
        )
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->root_url, 'presets_edit', array('{id}')),
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->root_url, 'presets_delete', array('{id}')),
            'confirm' => 'Удалить пресет "{title}"?'
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
