<?php

function grid_fields($controller){

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
            'width' => 30
        ),
        'title' => array(
            'title'    => LANG_CP_FIELD_TITLE,
            'href'     => href_to($controller->root_url, 'fields_edit', array('{id}')),
            'editable' => array(
                'table' => '{users}_fields'
            )
        ),
        'fieldset' => array(
            'title'   => LANG_CP_FIELD_FIELDSET,
            'width'   => 150,
            'handler' => function($value, $row) {
                return $value ? $value : '&mdash;';
            }
        ),
        'name' => array(
            'title' => LANG_SYSTEM_NAME,
            'width' => 150
        ),
        'handler_title' => array(
            'title' => LANG_CP_FIELD_TYPE,
            'width' => 150
        ),
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'fields_edit', array('{id}'))
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'fields_delete', array('{id}')),
            'confirm' => LANG_CP_FIELD_DELETE_CONFIRM,
            'handler' => function($row) {
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
