<?php

function grid_scheduler($controller){

    $options = array(
        'show_id' => false,
        'is_filter' => false,
    );

    $columns = array(
        'id' => array(

        ),
        'title' => array(
            'title' => LANG_CP_SCHEDULER_TASK,
        ),
        'controller' => array(
            'title' => LANG_CP_SCHEDULER_TASK_CONTROLLER,
            'width' => 150,
        ),
        'hook' => array(
            'title' => LANG_CP_SCHEDULER_TASK_HOOK,
            'width' => 150,
        ),
        'is_active' => array(
            'title' => LANG_IS_ENABLED,
            'flag'  => true,
            'width' => 60,
            'flag_toggle' => href_to($controller->name, 'settings/scheduler', array('toggle', '{id}'))
        ),
        'period' => array(
            'title' => LANG_CP_SCHEDULER_TASK_PERIOD,
            'width' => 150,
        ),
        'date_last_run' => array(
            'title' => LANG_CP_SCHEDULER_TASK_LAST_RUN,
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

