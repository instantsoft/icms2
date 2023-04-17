<?php

function grid_subscriptions($controller) {

    cmsCore::loadAllControllersLanguages();

    $options = [
        'is_sortable'   => false,
        'show_id'       => false,
        'is_selectable' => true,
        'order_by'      => 'subscribers_count',
        'order_to'      => 'desc'
    ];

    $columns  = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title'    => LANG_TITLE,
            'editable' => [],
            'filter' => 'like'
        ],
        'controller' => [
            'title'  => LANG_EVENTS_LISTENER,
            'class'  => 'd-none d-lg-table-cell',
            'width'  => 200,
            'filter' => 'exact',
            'filter_select' => [
                'items' => function ($name) {

                    $admin_model = cmsCore::getModel('admin');
                    $admin_model->join('subscriptions', 's', 's.controller = i.name');
                    $controllers = $admin_model->groupBy('i.id')->getInstalledControllers();

                    $items = ['' => LANG_ALL];
                    foreach ($controllers as $controller) {
                        $items[$controller['name']] = $controller['title'];
                    }
                    return $items;
                }
            ],
            'handler' => function ($val, $row) {
                return string_lang($val . '_CONTROLLER', $val);
            }
        ],
        'subject' => [
            'title' => LANG_CP_SUBJECT,
            'class' => 'd-none d-lg-table-cell',
            'width' => 200
        ],
        'subscribers_count' => [
            'title' => LANG_SBSCR_SUBSCRIBERS,
            'width' => 80
        ]
    ];

    $actions = [
        [
            'title'  => LANG_VIEW,
            'class'  => 'view',
            'target' => '_blank',
            'href'   => rel_to_href('{subject_url}')
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', ['{id}']),
            'confirm' => LANG_SBSCR_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
