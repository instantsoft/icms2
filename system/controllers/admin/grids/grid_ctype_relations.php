<?php

function grid_ctype_relations($controller){

    cmsCore::loadAllControllersLanguages();

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
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
                return constant('LANG_CP_RELATION_LAYOUT_' . strtoupper($value));
            }
        ),
        'target_controller' => array(
            'title' => LANG_EVENTS_LISTENER,
            'width' => 100,
            'handler' => function($value, $row){
                return string_lang('LANG_'.strtoupper($value).'_CONTROLLER');
            }
        )
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
