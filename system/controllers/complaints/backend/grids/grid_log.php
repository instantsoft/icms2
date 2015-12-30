<?php

function grid_log($controller){

    $options = array(
            'order_by' => 'id',
            'order_to' => 'asc',
    );

    $columns = array(
            'id' => array(
                'title' => 'id',
                'width' => 20
            ),                
            'orfo' => array(
                'title' => 'Текст ошибки',
                'width' => 300,
                'href' => '{url}'
            ),
            'comment' => array(
                'title' => 'Комментарий',
                'width' => 300
            ),
            'date' => array(
                'title' => 'Дата',
                'width' => 120
            ),
           'author' => array(
                'title' => 'Автор',
                'width' => 100
            )
    );

    $actions = array(
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->root_url, 'delete', '{id}'),
            'confirm' => LANG_COMPLAINTS_CP_FORM_DELETE_CONFIRM
        )
    );

    return array(
            'options' => $options,
            'columns' => $columns,
            'actions' => $actions
    );
}