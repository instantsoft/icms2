<?php

function grid_tags($controller){

    $options = array(
        'order_by' => 'tag',
        'order_to' => 'asc',
    );

    $columns = array(
        'tag' => array(
            'title' => LANG_TAGS_TAG,
            'href' => href_to($controller->root_url, 'edit', array('{id}')),
            'filter' => 'like'
        ),
        'frequency' => array(
            'title' => LANG_TAGS_TAG_FREQUENCY,
        ),
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'view',
            'href' => href_to('tags', '{tag|urlencode}')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->root_url, 'edit', array('{id}')),
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->root_url, 'delete', array('{id}')),
            'confirm' => LANG_TAGS_TAG_DELETE_CONFIRM,
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
