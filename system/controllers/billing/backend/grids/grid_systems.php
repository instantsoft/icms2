<?php

function grid_systems($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => true,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', 'billing_systems'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title' => LANG_TITLE,
            'href'  => href_to($controller->root_url, 'systems_edit', ['{id}'])
        ],
        'name' => [
            'title' => LANG_SYSTEM_NAME,
            'href'  => href_to($controller->root_url, 'systems_edit', ['{id}'])
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'billing_systems', 'is_enabled']),
            'width'       => 60
        ]
    ];

    $actions = [
        [
            'title' => LANG_CONFIG,
            'icon'  => 'cogs',
            'href'  => href_to($controller->root_url, 'systems_edit', ['{id}'])
        ],
        [
            'title' => sprintf(LANG_BILLING_CP_SYSTEM_LOG, '{title}'),
            'class' => 'ajax-modal',
            'icon'  => 'list-ul',
            'href'  => href_to($controller->root_url, 'systems_log', ['{id}'])
        ],
        [
            'title'  => LANG_HELP,
            'icon'   => 'question-circle',
            'target' => '_blank',
            'href'   => 'https://docs.instantcms.ru/manual/components/billing/systems/{name}'
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
