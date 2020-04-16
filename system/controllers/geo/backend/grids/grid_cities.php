<?php

function grid_cities($controller){

    $options = array(
        'is_sortable'   => true,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => true,
        'drag_save_url' => $controller->cms_template->href_to('cities_reorder'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => true
    );

    $columns = array(
		'id' => array(
            'title'  => 'ID',
            'class'  => 'd-none d-lg-table-cell',
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
        ),
		'ordering' => array(
			'title' => LANG_GEO_POSITION,
            'class' => 'd-none d-lg-table-cell',
			'width' => 60
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
