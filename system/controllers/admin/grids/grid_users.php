<?php

function grid_users($controller){

    $options = array(
        'is_auto_init' => false,
        'is_sortable' => true,
        'is_filter' => true,
        'is_pagination' => true,
        'is_draggable' => false,
        'order_by' => 'id',
        'order_to' => 'asc',
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
            'filter' => 'exact'
        ),
        'nickname' => array(
            'title' => LANG_NICKNAME,
            'href' => href_to($controller->name, 'users', array('edit', '{id}')),
            'filter' => 'like'
        ),
        'email' => array(
            'title' => LANG_EMAIL,
            'filter' => 'like'
        ),
        'date_reg' => array(
            'title' => LANG_REGISTRATION,
            'width' => 100,
            'filter' => 'like',
            'handler' => function($date){
                return date('Y-m-d', strtotime($date));
            }
        ),
        'karma' => array(
            'title' => LANG_KARMA,
            'width' => 60,
            'filter' => 'exact',
            'handler' => function($value){
                return '<span class="'.  html_signed_class($value).'">'.html_signed_num($value).'</span>';
            }
        ),
        'rating' => array(
            'title' => LANG_RATING,
            'width' => 60,
            'filter' => 'exact'
        ),
        'is_locked' => array(
            'title' => LANG_CP_USER_LOCKED,
            'flag' => 'flag_lock',
            'width' => 24
        ),
    );

    $actions = array(
        array(
            'title' => LANG_PROFILE,
            'class' => 'view',
            'href' => href_to('users', '{id}')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->name, 'users', array('edit', '{id}'))
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->name, 'users', array('delete', '{id}')),
            'confirm' => LANG_CP_USER_DELETE_CONFIRM
        ),
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

