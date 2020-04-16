<?php

function grid_migrations($controller){

    $options = array(
        'is_filter' => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'class' => 'd-none d-lg-table-cell',
            'width' => 30
        ),
        'title' => array(
            'title'    => LANG_USERS_MIG_TITLE,
            'href'     => href_to($controller->root_url, 'migrations_edit', array('{id}')),
            'editable' => array(
                'table' => '{users}_groups_migration'
            )
        ),
        'passed_days' => array(
            'title'   => LANG_USERS_MIG_PASSED_DAYS,
            'handler' => function($val) {
                return $val ? $val : '&mdash;';
            },
            'width'   => 80
        ),
        'rating' => array(
            'title'   => LANG_RATING,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function($val) {
                return $val ? $val : '&mdash;';
            },
            'width'  => 80
        ),
        'karma' => array(
            'title'   => LANG_KARMA,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function($val) {
                return $val ? $val : '&mdash;';
            },
            'width' => 80
        ),
        'is_active' => array(
            'title' => LANG_ON,
            'flag'  => true,
            'width' => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', '{users}_groups_migration', 'is_active'))
        )
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'migrations_edit', array('{id}'))
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'migrations_delete', array('{id}')),
            'confirm' => LANG_USERS_MIG_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
