<?php

function grid_controllers_events ($controller){

    cmsCore::loadAllControllersLanguages();

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_draggable'  => true,
        'is_pagination' => false,
        'order_by'      => 'ordering',
        'order_to'      => 'asc'
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30
        ),
        'event' => array(
            'title'  => LANG_EVENTS_EVENT_NAME,
            'filter' => 'like'
        ),
        'listener' => array(
            'title'  => LANG_EVENTS_LISTENER,
            'width'  => 200,
            'filter' => 'like',
            'filter_select' => array(
                'items' => function($name){
                    $admin_model = cmsCore::getModel('admin');
                    $controllers = $admin_model->getInstalledControllers();
                    $items = array('' => '');
                    foreach($controllers as $controller){
                        $items[$controller['name']] = $controller['title'];
                    }
                    return $items;
                }
            ),
            'handler' => function($val, $row){
                return string_lang($val.'_CONTROLLER', $val);
            }
        ),
        'ordering' => array(
            'title' => LANG_ORDER,
            'width' => 70
        ),
        'is_enabled' => array(
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->name, 'controllers', array('events_toggle', '{id}')),
            'width'       => 80
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => array()
    );

}
