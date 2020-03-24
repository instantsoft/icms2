<?php

function grid_tabs($controller){

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => $controller->cms_template->href_to('tabs_reorder'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30
        ),
        'title' => array(
            'title'    => LANG_CP_TAB_TITLE,
            'href'     => href_to($controller->root_url, 'tabs_edit', array('{id}')),
            'editable' => array(
                'table' => '{users}_tabs'
            )
        ),
        'name' => array(
            'title' => LANG_SYSTEM_NAME,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150
        ),
        'is_active' => array(
            'title' => LANG_SHOW,
            'flag'  => true,
            'width' => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', '{users}_tabs', 'is_active'))
        )
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'tabs_edit', array('{id}'))
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
