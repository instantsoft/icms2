<?php

function grid_ctype_filters($controller, $ctype = []){

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => false,
        'order_by'      => 'id',
        'order_to'      => 'asc',
        'show_id'       => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30
        ),
        'title' => array(
            'title'    => LANG_TITLE,
            'href'     => href_to($controller->name, 'ctypes', array('filters_add', $ctype['id'], '{id}', 'edit'))
        ),
        'slug' => array(
            'title' => LANG_SYSTEM_NAME,
            'width' => 350
        )
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'view',
            'href'  => href_to($ctype['name'], '{slug}')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'ctypes', array('filters_add', $ctype['id'], '{id}', 'edit'))
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'ctypes', array('filters_delete', $ctype['id'], '{id}')),
            'confirm' => LANG_CP_FILTER_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
