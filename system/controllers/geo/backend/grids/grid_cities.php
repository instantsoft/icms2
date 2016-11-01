<?php

function grid_cities($controller){

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'order_by'      => 'name',
        'order_to'      => 'asc',
        'show_id'       => true
    );

    $columns = array(
		'id' => array(
            'title'  => 'ID',
            'width'  => 20,
            'filter' => 'exact'
        ),
        'name' => array(
            'title'  => LANG_TITLE,
            'href'   => href_to($controller->root_url, 'city', array('{id}')),
            'filter' => 'like',
            'editable' => array(
                'table' => 'geo_cities'
            )
        )
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'city', array('{id}'))
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', array('city', '{id}')),
            'confirm' => LANG_GEO_DELETE_CITY
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
