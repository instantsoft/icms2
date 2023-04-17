<?php

function grid_types($controller) {

    cmsCore::loadAllControllersLanguages();

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'show_id'       => false,
        'order_by'      => 'controller',
        'order_to'      => 'asc'
    ];

    $columns = [
        'id' => [
            'title'  => 'id',
            'width'  => 30
        ],
        'controller' => [
            'title'  => LANG_EVENTS_LISTENER,
            'width'  => 200,
            'class' => 'd-none d-lg-table-cell',
            'filter' => 'exact',
            'filter_select' => [
                'items' => function($name){
                    $admin_model = cmsCore::getModel('admin');
                    $admin_model->join('activity_types', 'e', 'e.controller = i.name');
                    $controllers = $admin_model->groupBy('i.id')->getInstalledControllers();
                    $items = ['' => LANG_ALL];
                    foreach($controllers as $controller){
                        $items[$controller['name']] = $controller['title'];
                    }
                    return $items;
                }
            ],
            'handler' => function($val, $row){
                return string_lang($val.'_CONTROLLER', $val);
            }
        ]
    ];

    if(cmsConfig::get('is_user_change_lang')){

        $langs = cmsCore::getDirsList('system/languages', true);

        $default_lang = cmsConfig::get('language');

        foreach ($langs as $lang) {

            $name = 'description';

            if($lang !== $default_lang){
                $name = 'description_'.$lang;
            }

            $columns[$name] = [
                'title' => LANG_ACTIVITY_DESC.' ['.$lang.']',
                'editable' => [
                    'language_context' => true
                ]
            ];
        }

    } else {

        $columns['description'] = [
            'title' => LANG_ACTIVITY_DESC,
            'editable' => []
        ];
    }

    $actions = [];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
