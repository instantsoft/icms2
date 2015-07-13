<?php

function grid_controllers($controller){

    $options = array(
        'order_by' => 'title',
        'is_pagination' => false,
    );

    $columns = array(
        'title' => array(
            'title' => LANG_TITLE,
            'href' => href_to($controller->name, 'controllers', array('edit', '{name}')),
            'filter' => 'like'
        ),
        'version' => array(
            'title' => LANG_VERSION,
            'width' => 150,
            'filter' => 'like'
        ),
        'author' => array(
            'title' => LANG_AUTHOR,
            'width' => 150,
            'href' => '{author}',
            'filter' => 'like'
        )
    );

    $actions = array(
        array(
            'title' => LANG_CONFIG,
            'class' => 'config',
            'href' => href_to($controller->name, 'controllers', array('edit', '{name}')),
            'handler' => function($row){
                return $row['is_backend'];
            }
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

