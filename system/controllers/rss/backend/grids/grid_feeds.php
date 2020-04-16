<?php

function grid_feeds($controller){

    $options = array(
        'order_by' => 'title',
        'order_to' => 'asc',
    );

    $columns = array(
        'title' => array(
            'title' => LANG_RSS_FEED_TITLE,
            'href' => href_to($controller->root_url, 'edit', array('{id}')),
            'filter' => 'like'
        ),
        'is_enabled' => array(
            'title' => LANG_IS_ENABLED,
            'flag'  => true,
            'width' => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'rss_feeds', 'is_enabled'))
        ),
        'is_cache' => array(
            'title' => LANG_RSS_FEED_IS_CACHE,
            'flag'  => true,
            'width' => 60,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'rss_feeds', 'is_cache'))
        ),
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'rss',
            'href' => href_to('rss', 'feed', '{ctype_name}')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->root_url, 'edit', array('{id}')),
        ),
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

