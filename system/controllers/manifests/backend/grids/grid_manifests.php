<?php

function grid_manifests($controller){

    cmsCore::loadAllControllersLanguages();

    $options = array(
        'is_sortable' => true,
        'is_filter' => false,
        'is_draggable' => true,
        'order_by' => 'name',
        'order_to' => 'asc',
        'is_pagination' => false,
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30
        ),
        'controller' => array(
            'title' => LANG_MANIFESTS_CONTROLLER_NAME,
            'width' => 200,
            'handler' => function($val, $row){
                return string_lang($val.'_CONTROLLER', $val);
            }
        ),
        'name' => array(
            'title' => LANG_MANIFESTS_HOOK_NAME,
        ),
        'ordering' => array(
            'title' => LANG_ORDER,
            'width' => 70
        ),
        'is_enabled' => array(
            'title' => LANG_IS_ENABLED,
            'flag' => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'controllers_hooks', 'is_enabled')),
            'width' => 80
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => array()
    );

}