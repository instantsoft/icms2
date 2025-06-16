<?php

function grid_terms($controller) {

    $groups = cmsCore::getModel('users')->getGroups();

    $content_model = cmsCore::getModel('content');

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'is_selectable' => false,
        'order_by'      => 'id',
        'order_to'      => 'desc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'ctype_id' => [
            'title'   => LANG_CONTENT_TYPE,
            'href'    => href_to($controller->root_url, 'prices', ['terms_edit', '{id}']),
            'handler' => function ($value, $row) {
                return $row['ctype_title'];
            },
            'filter' => 'exact',
            'filter_select' => [
                'items' => function ($name)use ($content_model) {

                    $items = ['' => LANG_ALL];

                    $ctypes  = $content_model->getContentTypes();

                    if ($ctypes) {
                        $items += array_collection_to_list($ctypes, 'id', 'title');
                    }

                    return $items;
                }
            ]
        ],
        'prices'  => [
            'title' => LANG_BILLING_PLAN_PRICES_PRICE,
            'handler' => function ($value, $row) use($groups) {

                $value = array_filter(cmsModel::yamlToArray($value));

                if (!$value) {
                    return '&mdash;';
                }

                $prices = [];

                foreach ($value as $group_id => $price) {
                    $prices[] = $groups[$group_id]['title'] . ': ' . $price;
                }

                return implode(', ', $prices);
            }
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'prices', ['terms_edit', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'confirm' => LANG_BILLING_CP_FIELDS_DELETE,
            'href'    => href_to($controller->root_url, 'prices', ['terms_delete', '{id}'])
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
