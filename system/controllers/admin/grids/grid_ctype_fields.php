<?php

function grid_ctype_fields($controller){

    $options = array(
        'is_sortable' => false,
        'is_filter' => false,
        'is_pagination' => false,
        'is_draggable' => true,
        'order_by' => 'ordering',
        'order_to' => 'asc',
        'show_id' => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
        ),
        'title' => array(
            'title' => LANG_CP_FIELD_TITLE,
            'href' => href_to($controller->name, 'ctypes', array('fields_edit', '{ctype_id}', '{id}')),
        ),
        'fieldset' => array(
            'title' => LANG_CP_FIELD_FIELDSET,
            'handler' => function($value, $row){
                return $value ? $value : '&mdash;';
            }
        ),
        'is_in_list' => array(
            'title' => LANG_CP_FIELD_IN_LIST_SHORT,
            'flag'  => true,
			'flag_toggle' => href_to($controller->name, 'ctypes', array('fields_toggle', 'list', '{ctype_id}', '{id}')),
            'width' => 60,
        ),
        'is_in_item' => array(
            'title' => LANG_CP_FIELD_IN_ITEM_SHORT,
            'flag'  => true,
			'flag_toggle' => href_to($controller->name, 'ctypes', array('fields_toggle', 'item', '{ctype_id}', '{id}')),
            'width' => 60,
        ),
        'name' => array(
            'title' => LANG_SYSTEM_NAME,
            'width' => 120,
        ),
        'handler_title' => array(
            'title' => LANG_CP_FIELD_TYPE,
            'width' => 150,
        ),
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->name, 'ctypes', array('fields_edit', '{ctype_id}', '{id}'))
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->name, 'ctypes', array('fields_delete', '{ctype_id}', '{id}')),
            'confirm' => LANG_CP_FIELD_DELETE_CONFIRM,
            'handler' => function($row){
                return !$row['is_fixed'];
            }
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

