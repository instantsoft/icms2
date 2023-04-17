<?php

function grid_scheduler($controller) {

    $options = [
        'is_sortable'   => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', ['scheduler_tasks']),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false,
        'is_filter'     => true
    ];

    $columns = [
        'id'         => [],
        'title'      => [
            'title' => LANG_CP_SCHEDULER_TASK,
            'href'  => href_to($controller->name, 'settings', ['scheduler', 'edit', '{id}']),
        ],
        'controller' => [
            'title'         => LANG_CP_SCHEDULER_TASK_CONTROLLER,
            'class'         => 'd-none d-lg-table-cell',
            'width'         => 150,
            'filter'        => 'exact',
            'filter_select' => [
                'items' => function ($name)use ($controller) {

                    $controllers = $controller->model->getInstalledControllers();
                    $tasks_controllers = $controller->model->selectOnly('controller')->get('scheduler_tasks', function($item, $model){
                        return $item['controller'];
                    }, 'controller');

                    $items = ['' => LANG_ALL];
                    foreach ($controllers as $cont) {
                        if(!empty($tasks_controllers[$cont['name']])){
                            $items[$cont['name']] = $cont['title'];
                        }
                    }
                    return $items;
                }
            ]
        ],
        'hook' => [
            'title' => LANG_CP_SCHEDULER_TASK_HOOK,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150
        ],
        'is_active' => [
            'title'       => LANG_IS_ENABLED,
            'class'       => 'd-none d-sm-table-cell',
            'flag'        => true,
            'width'       => 60,
            'flag_toggle' => href_to($controller->name, 'toggle_item', ['{id}', 'scheduler_tasks', 'is_active']),
        ],
        'period' => [
            'title' => LANG_CP_SCHEDULER_TASK_PERIOD,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150
        ],
        'date_last_run' => [
            'title'   => LANG_CP_SCHEDULER_TASK_LAST_RUN,
            'class'   => 'd-none d-md-table-cell',
            'width'   => 150,
            'handler' => function ($value) {
                return (empty($value) ? '&mdash;' : html_date_time($value));
            }
        ]
    ];

    $actions = [
        [
            'title' => LANG_CP_SCHEDULER_TASK_RUN,
            'class' => 'play',
            'href'  => href_to($controller->name, 'settings', ['scheduler', 'run', '{id}'])
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'settings', ['scheduler', 'edit', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'settings', ['scheduler', 'delete', '{id}']),
            'confirm' => LANG_CP_SCHEDULER_TASK_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
