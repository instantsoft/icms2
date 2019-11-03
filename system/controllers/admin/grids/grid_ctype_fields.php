<?php

function grid_ctype_fields($controller, $ctype_name){

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => $controller->cms_template->href_to('ctypes', array('fields_reorder', $ctype_name)),
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
            'title' => LANG_CP_FIELD_TITLE,
            'href' => href_to($controller->name, 'ctypes', array('fields_edit', '{ctype_id}', '{id}')),
        ),
        'fieldset' => array(
            'title' => LANG_CP_FIELD_FIELDSET,
            'class' => 'd-none d-lg-table-cell',
            'handler' => function($value, $row){
                return $value ? $value : '&mdash;';
            }
        ),
        'is_enabled' => array(
            'title' => LANG_IS_ENABLED,
			'flag' => true,
			'flag_toggle' => href_to($controller->name, 'ctypes', array('fields_toggle', 'enable', '{ctype_id}', '{id}')),
            'width' => 80
        ),
        'is_in_list' => array(
            'title' => LANG_CP_FIELD_IN_LIST_SHORT,
            'class' => 'd-none d-md-table-cell',
            'flag'  => true,
			'flag_toggle' => href_to($controller->name, 'ctypes', array('fields_toggle', 'list', '{ctype_id}', '{id}')),
            'width' => 60,
            'flag_handler' => function($value, $row){
                if(!empty($row['options']['context_list']) && array_search('0', $row['options']['context_list']) === false){
                    return -1;
                }
                return $value;
            }
        ),
        'is_in_item' => array(
            'title' => LANG_CP_FIELD_IN_ITEM_SHORT,
            'class' => 'd-none d-md-table-cell',
            'flag'  => true,
			'flag_toggle' => href_to($controller->name, 'ctypes', array('fields_toggle', 'item', '{ctype_id}', '{id}')),
            'width' => 60,
        ),
        'name' => array(
            'title' => LANG_SYSTEM_NAME,
            'class' => 'd-none d-lg-table-cell',
            'width' => 120,
        ),
        'handler_title' => array(
            'title' => LANG_CP_FIELD_TYPE,
            'class' => 'd-none d-lg-table-cell',
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
