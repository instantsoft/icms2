<?php

function grid_tags($controller) {

    $options = [
        'order_by' => 'tag',
        'order_to' => 'asc',
    ];

    $columns = [
        'tag'       => [
            'title'  => LANG_TAGS_TAG,
            'href'   => href_to($controller->root_url, 'edit', ['{id}']),
            'filter' => 'like'
        ],
        'frequency' => [
            'title' => LANG_TAGS_TAG_FREQUENCY,
        ]
    ];

    $actions = [
        [
            'title'  => LANG_VIEW,
            'class'  => 'view',
            'target' => '_blank',
            'href'   => href_to('tags', '{tag|string_urlencode}')
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'edit', ['{id}']),
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', ['{id}']),
            'confirm' => LANG_TAGS_TAG_DELETE_CONFIRM,
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
