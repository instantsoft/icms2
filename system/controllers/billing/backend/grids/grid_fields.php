<?php

function grid_fields($controller) {

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
            'href'    => href_to($controller->root_url, 'prices', ['fields_edit', '{id}']),
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
        'field'  => [
            'title' => LANG_CP_FIELD,
            'href'  => href_to($controller->root_url, 'prices', ['fields_edit', '{id}']),
            'handler' => function ($value, $row) use($content_model) {

                $field = $content_model->getContentField($row['ctype_name'], $value, 'name');

                return $field['title'] ?? $value;
            }
        ],
        'price_field'  => [
            'title' => LANG_BILLING_PLAN_PRICES_PRICE,
            'handler' => function ($value, $row) use($content_model, $groups) {

                if (!$value) {

                    $row['prices'] = array_filter(cmsModel::yamlToArray($row['prices']));

                    if (!$row['prices']) {
                        return '&mdash;';
                    }

                    $prices = [];

                    foreach ($row['prices'] as $group_id => $price) {
                        $prices[] = $groups[$group_id]['title'] . ': ' . $price;
                    }

                    return implode(', ', $prices);
                }

                $field = $content_model->getContentField($row['ctype_name'], $value, 'name');

                return sprintf(LANG_BILLING_CP_FIELD_PRICE_FIELD_LIST, ($field['title'] ?? $value));
            }
        ],
        'is_to_author'  => [
            'title' => LANG_BILLING_CP_PROFIT,
            'handler' => function ($value, $row)  {

                $types = [
                    0 => LANG_BILLING_CP_PROFIT_0,
                    1 => LANG_BILLING_CP_PROFIT_1
                ];

                return $types[$value] ?? '';
            }
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'prices', ['fields_edit', '{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'confirm' => LANG_BILLING_CP_FIELDS_DELETE,
            'href'    => href_to($controller->root_url, 'prices', ['fields_delete', '{id}'])
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
