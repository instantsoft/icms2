<?php

function grid_ctype_props($controller, $drag_save_url){

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => $drag_save_url,
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'class' => 'd-none d-lg-table-cell',
            'width' => 30,
        ),
        'title' => array(
            'title' => LANG_CP_FIELD_TITLE,
            'href' => href_to($controller->name, 'ctypes', array('props_edit', '{ctype_id}', '{prop_id}')),
        ),
        'fieldset' => array(
            'title' => LANG_CP_FIELD_FIELDSET,
            'class' => 'd-none d-lg-table-cell',
            'handler' => function($value, $row){
                return $value ? $value : '&mdash;';
            }
        ),
        'is_in_filter' => array(
            'title' => LANG_FILTER,
            'class' => 'd-none d-lg-table-cell',
            'flag'  => true,
			'flag_toggle' => href_to($controller->name, 'ctypes', array('props_toggle', '{ctype_id}', '{prop_id}')),
            'width' => 60,
        ),
        'type' => array(
            'title' => LANG_CP_FIELD_TYPE,
            'width' => 150,
            'handler' => function($value, $row){
                return constant('LANG_PARSER_'.mb_strtoupper($value));
            },
        ),
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'ctypes', array('props_edit', '{ctype_id}', '{prop_id}'))
        ),
        array(
            'title'   => LANG_CP_PROPS_UNBIND,
            'class'   => 'unbind',
            'href'    => href_to($controller->name, 'ctypes', array('props_unbind', '{ctype_id}', '{prop_id}', '{cat_id}')),
            'confirm' => LANG_CP_PROPS_UNBIND_CONFIRM
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', array('props_delete', '{ctype_id}', '{prop_id}')),
            'confirm' => LANG_CP_PROPS_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

