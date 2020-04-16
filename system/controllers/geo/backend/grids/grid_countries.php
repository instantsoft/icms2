<?php

function grid_countries($controller){

    $options = array(
        'is_sortable'   => true,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => true,
        'drag_save_url' => $controller->cms_template->href_to('countries_reorder'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => true
    );

    $columns = array(
		'id' => array(
            'title'  => 'ID',
            'width'  => 20,
            'class'  => 'd-none d-lg-table-cell',
            'filter' => 'exact'
        ),
        'name' => array(
            'title'    => LANG_TITLE,
            'href'     => href_to($controller->root_url, 'regions', array('{id}')),
            'filter'   => 'like',
            'editable' => array(
                'table' => 'geo_countries'
            )
        ),
		'alpha2' => array(
            'title'  => LANG_GEO_ALPHA2,
            'width'  => 250,
            'filter' => 'like',
            'editable' => array(
                'table' => 'geo_countries'
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
            'href'  => href_to($controller->root_url, 'country', array('{id}'))
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', array('country', '{id}')),
            'confirm' => LANG_GEO_DELETE_COUNTRY
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
