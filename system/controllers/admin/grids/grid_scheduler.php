<?php

function grid_scheduler($controller){

    $options = array(
        'is_sortable'   => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', ['scheduler_tasks']),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false,
        'is_filter'     => true
    );

    $columns = array(
        'id' => array(),
        'title' => array(
            'title' => LANG_CP_SCHEDULER_TASK,
            'href' => href_to($controller->name, 'settings', array('scheduler', 'edit', '{id}')),
        ),
        'controller' => array(
            'title' => LANG_CP_SCHEDULER_TASK_CONTROLLER,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150,
            'filter' => 'like',
            'filter_select' => array(
                'items' => function($name)use($controller){
                    $controllers = $controller->model->getInstalledControllers();
                    $items = array('' => '');
                    foreach($controllers as $cont){
                        $items[$cont['name']] = $cont['title'];
                    }
                    return $items;
                }
            )
        ),
        'hook' => array(
            'title' => LANG_CP_SCHEDULER_TASK_HOOK,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150,
        ),
        'is_active' => array(
            'title' => LANG_IS_ENABLED,
            'class' => 'd-none d-sm-table-cell',
            'flag'  => true,
            'width' => 60,
            'flag_toggle' => href_to($controller->name, 'settings/scheduler', array('toggle', '{id}'))
        ),
        'period' => array(
            'title' => LANG_CP_SCHEDULER_TASK_PERIOD,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150,
        ),
        'date_last_run' => array(
            'title' => LANG_CP_SCHEDULER_TASK_LAST_RUN,
            'class' => 'd-none d-md-table-cell',
            'width' => 150,
            'handler' => function($value){
                return (empty($value) ? '&mdash;' : html_date_time($value));
            }
        ),
    );

    $actions = array(
        array(
            'title' => LANG_CP_SCHEDULER_TASK_RUN,
            'class' => 'play',
            'href' => href_to($controller->name, 'settings', array('scheduler', 'run', '{id}'))
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->name, 'settings', array('scheduler', 'edit', '{id}'))
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->name, 'settings', array('scheduler', 'delete', '{id}')),
            'confirm' => LANG_CP_SCHEDULER_TASK_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
