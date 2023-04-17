<?php

function grid_controllers_events($controller) {

    cmsCore::loadAllControllersLanguages();

    $options = [
        'show_id'       => false,
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', ['events']),
        'is_pagination' => false,
        'order_by'      => 'ordering',
        'order_to'      => 'asc'
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'event' => [
            'title'  => LANG_EVENTS_EVENT_NAME,
            'filter' => 'like'
        ],
        'listener' => [
            'title'  => LANG_EVENTS_LISTENER,
            'width'  => 280,
            'class'  => 'd-none d-lg-table-cell',
            'filter' => 'in',
            'filter_select' => array(
                'items' => function ($name) {
                    $admin_model = cmsCore::getModel('admin');
                    $admin_model->join('events', 'e', 'e.listener = i.name');
                    $controllers = $admin_model->groupBy('i.id')->getInstalledControllers();
                    $items       = [];
                    foreach ($controllers as $controller) {
                        $items[$controller['name']] = $controller['title'];
                    }
                    return $items;
                }
            ),
            'handler' => function ($val, $row) {
                return string_lang($val . '_CONTROLLER', $val);
            }
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->name, 'controllers', ['events_toggle', '{id}']),
            'width'       => 80
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => []
    ];
}
