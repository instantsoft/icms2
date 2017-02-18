<?php

function grid_ctype_relations($controller){

    $options = array(
        'is_sortable' => true,
        'is_filter' => false,
        'is_pagination' => false,
        'is_draggable' => false,
        'order_by' => 'id',
        'order_to' => 'asc',
        'show_id' => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
        ),
        'title' => array(
            'title' => LANG_CP_RELATION_TITLE,
            'href' => href_to($controller->name, 'ctypes', array('relations_edit', '{ctype_id}', '{id}')),
        ),
        'layout' => array(
            'title' => LANG_CP_RELATION_LAYOUT_TYPE,
            'handler' => function($value, $row){
                return constant('LANG_CP_RELATION_LAYOUT_' . mb_strtoupper($value));
            }
        ),
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->name, 'ctypes', array('relations_edit', '{ctype_id}', '{id}'))
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->name, 'ctypes', array('relations_delete', '{id}')),
            'confirm' => LANG_CP_RELATION_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
