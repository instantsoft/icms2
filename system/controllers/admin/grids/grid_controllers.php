<?php

function grid_controllers($controller) {

    $denied = [
        'admin', 'auth', 'images', 'content', 'moderation', 'users'
    ];

    $options = [
        'order_by'      => false,
        'order_to'      => false,
        'is_pagination' => false
    ];

    $columns = [
        'title' => [
            'title'        => LANG_TITLE,
            'href'         => href_to($controller->name, 'controllers', ['edit', '{name}']),
            'filter'       => 'like',
            'href_handler' => function ($item) {
                return $item['is_backend'];
            }
        ],
        'slug' => [
            'title'    => LANG_ADMIN_CONTROLLER_SLUG,
            'class'    => 'd-none d-lg-table-cell',
            'editable' => [
                'attributes' => ['placeholder' => '{name}'],
                'rules' => [
                    ['sysname']
                ]
            ],
            'handler' => function ($v, $row) {
                if (!$v) {
                    return $row['name'];
                }
                return $v;
            }
        ],
        'is_enabled' => [
            'title'        => LANG_IS_ENABLED,
            'flag'         => true,
            'flag_toggle'  => href_to($controller->name, 'controllers', ['toggle', '{id}']),
            'href_handler' => function ($row) use ($denied) {
                if (in_array($row['name'], $denied)) {
                    return false;
                }
                return true;
            }
        ],
        'version' => [
            'title'  => LANG_VERSION,
            'class'  => 'd-none d-lg-table-cell',
            'width'  => 70,
            'filter' => 'like'
        ],
        'author'  => [
            'title'  => LANG_AUTHOR,
            'class'  => 'd-none d-lg-table-cell',
            'width'  => 200,
            'href'   => '{url}',
            'filter' => 'like'
        ]
    ];

    $actions = [
        [
            'title'   => LANG_CP_PACKAGE_CONTENTS,
            'class'   => 'view ajax-modal',
            'href'    => href_to($controller->name, 'package_files_list', ['controllers', '{id}']),
            'handler' => function ($row) {
                return $row['files'];
            }
        ],
        [
            'title'   => LANG_CONFIG,
            'class'   => 'config',
            'href'    => href_to($controller->name, 'controllers', ['edit', '{name}']),
            'handler' => function ($row) {
                return $row['is_backend'];
            }
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'confirm' => LANG_CP_DELETE_COMPONENT_CONFIRM,
            'href'    => href_to($controller->name, 'controllers_delete', ['{name}']),
            'handler' => function ($row) {
                return $row['is_external'];
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
