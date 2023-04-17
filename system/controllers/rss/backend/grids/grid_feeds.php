<?php

function grid_feeds($controller) {

    $options = [
        'order_by' => 'title',
        'order_to' => 'asc',
    ];

    $columns = [
        'title' => [
            'title'  => LANG_RSS_FEED_TITLE,
            'href'   => href_to($controller->root_url, 'edit', ['{id}']),
            'filter' => 'like'
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'width'       => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'rss_feeds', 'is_enabled'])
        ],
        'is_cache' => [
            'title'       => LANG_RSS_FEED_IS_CACHE,
            'flag'        => true,
            'width'       => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'rss_feeds', 'is_cache'])
        ]
    ];

    $actions = [
        [
            'title'  => LANG_VIEW,
            'target' => '_blank',
            'class'  => 'rss',
            'href'   => href_to('rss', 'feed', '{ctype_name}')
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'edit', ['{id}']),
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
