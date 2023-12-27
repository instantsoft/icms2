<?php

function grid_content_items($controller, $ctype) {

    $fields = $controller->model_backend_content->getContentFields($ctype['name']);

    $options = [
        'advanced_filter' => $controller->cms_template->href_to('content', ['filter', $ctype['id']]),
        'is_sortable'     => true,
        'is_filter'       => true,
        'is_pagination'   => true,
        'is_draggable'    => false,
        'is_selectable'   => true,
        'select_actions'  => [
            [
                'title'  => LANG_CP_CONTENT_ITEMS_EDIT,
                'action' => 'open',
                'url'    => $controller->cms_template->href_to('content', ['items_edit', '{0}'])
            ],
            [
                'title'  => LANG_MOVE,
                'action' => 'open',
                'url'    => $controller->cms_template->href_to('content', ['item_move', '{0}', '{1}'])
            ],
            [
                'title'   => LANG_DELETE,
                'action'  => 'submit',
                'confirm' => LANG_DELETE_SELECTED_CONFIRM,
                'url'     => $controller->cms_template->href_to('content', ['item_delete', '{0}'])
            ],
            [
                'title'   => LANG_BASKET_DELETE,
                'action'  => 'submit',
                'confirm' => LANG_TRASH_DELETE_SELECTED_CONFIRM,
                'url'     => $controller->cms_template->href_to('content', ['item_trash_put', '{0}'])
            ]
        ],
        'order_by'        => 'date_pub',
        'order_to'        => 'desc',
        'show_id'         => true
    ];

    $columns = [
        'id' => [
            'title'      => 'id',
            'switchable' => true,
            'disable'    => true
        ],
        'title' => [
            'title'  => LANG_TITLE,
            'href'   => href_to($ctype['name'], 'edit', '{id}') . '?back=' . href_to($controller->name, 'content'),
            'filter' => 'like'
        ],
        'date_pub' => [
            'title'      => LANG_DATE,
            'class'      => 'd-none d-lg-table-cell',
            'switchable' => true,
            'filter'     => 'date',
            'handler'    => function ($value, $item) {
                return html_date($value, true);
            }
        ],
        'is_approved' => [
            'title'      => LANG_MODERATION,
            'width'      => 150,
            'switchable' => true,
            'handler' => function ($value, $item) use ($controller, $ctype) {
                if ($item['is_deleted']) {
                    $string = '<a href="' . href_to($controller->name, 'controllers', ['edit', 'moderation', 'logs', 'content', $ctype['name'], $item['id']]) . '">';
                    if ($item['trash_date_expired']) {
                        $expired = ((time() - strtotime($item['trash_date_expired'])) > 0) ? true : false;
                        $string  .= sprintf(LANG_MODERATION_IN_TRASH_TIME, ($expired ? '-' : '') . string_date_age_max($item['trash_date_expired']));
                    } else {
                        $string .= LANG_MODERATION_IN_TRASH;
                    }
                    $string .= '</a>';
                    return $string;
                }
                if ($item['is_approved']) {
                    if ($item['approved_by']) {
                        return html_bool_span(LANG_MODERATION_SUCCESS, true);
                    } else {
                        return html_bool_span(LANG_MODERATION_NOT_NEEDED, true);
                    }
                } else {
                    if (!empty($item['is_draft'])) {
                        return html_bool_span(LANG_CONTENT_DRAFT_NOTICE, false);
                    }
                    return html_bool_span(LANG_CONTENT_NOT_APPROVED, false);
                }
            }
        ],
        'is_pub' => [
            'title'       => LANG_ON,
            'width'       => 40,
            'flag'        => true,
            'switchable'  => true,
            'flag_toggle' => href_to($controller->name, 'content', ['item_toggle', $ctype['name'], '{id}'])
        ],
        'user_id' => [
            'title'      => LANG_AUTHOR,
            'class'      => 'd-none d-lg-table-cell',
            'switchable' => true,
            'href'       => href_to('users', '{user_id}'),
            'key_alias'  => 'user_nickname',
            'order_by'   => 'u.nickname'
        ]
    ];

    if ($ctype['is_rating']) {

        $columns['rating'] = [
            'title'      => LANG_RATING,
            'filter'     => 'exact',
            'switchable' => true,
            'disable'    => true
        ];
    }
    if ($ctype['is_comments']) {

        $columns['comments'] = [
            'title'      => LANG_COMMENTS,
            'filter'     => 'exact',
            'switchable' => true,
            'disable'    => true
        ];
    }
    if (!empty($ctype['options']['hits_on'])) {

        $columns['hits_count'] = [
            'title'      => LANG_HITS,
            'filter'     => 'exact',
            'switchable' => true,
            'disable'    => true
        ];
    }

    foreach ($fields as $name => $field) {

        if ($field['handler']->is_virtual ||
                $field['is_system'] ||
                $field['is_fixed'] ||
                isset($columns[$name])) {
            continue;
        }

        $filter = $filter_select = $handler = $flag = null;
        if (in_array($field['type'], ['number', 'html', 'string', 'text', 'url'])) {
            $filter = 'like';
        }
        if (in_array($field['type'], ['date', 'age'])) {
            $filter = 'date';
        }
        if ($field['type'] === 'checkbox') {
            $flag = true;
            $filter = 'filled';
            $filter_select = ['items' => ['' => LANG_ALL, '1' => LANG_ON, '0' => LANG_OFF]];
        }
        if (in_array($field['type'], ['html', 'text'])) {
            $handler = function ($value, $item) {
                return string_short($value, 100);
            };
        }
        if ($field['type'] === 'images') {
            $handler = function ($value, $item) {
                return $value ? count(!is_array($value) ? cmsModel::yamlToArray($value) : $value) : 0;
            };
        }
        if ($field['type'] === 'image') {
            $presets = [$field['handler']->getOption('size_teaser'), $field['handler']->getOption('size_full')];
            $handler = function ($value, $item) use ($presets) {
                if (!$value) {
                    return '';
                }
                $value = !is_array($value) ? cmsModel::yamlToArray($value) : $value;
                return html_image($value, $presets, '', ['class' => 'grid_image_preview img-thumbnail']);
            };
        }
        if (in_array($field['type'], ['list', 'listbitmask', 'listmultiple'])) {

            $filter = 'exact';
            $filter_select = ['items' => ['' => LANG_ALL] + $field['handler']->getListItems()];

            $handler = function ($value, $item)use ($field, $ctype) {
                if (!$value) {
                    return '';
                }
                $item['ctype']      = $ctype;
                $item['ctype_name'] = $ctype['name'];
                $field['handler']->setItem($item);
                return $field['handler']->parseTeaser($value);
            };
        }
        if ($field['handler']->is_denormalization) {
            $handler = function ($value, $item)use ($field) {
                if (!$value) {
                    return '';
                }
                return $item[$field['handler']->getDenormalName()];
            };
        }

        $columns[$name] = [
            'title'         => $field['title'],
            'flag'          => $flag,
            'filter'        => $filter,
            'filter_select' => $filter_select,
            'handler'       => $handler,
            'switchable'    => true,
            'disable'       => true
        ];
    }

    $actions = [
        [
            'title'  => LANG_VIEW,
            'class'  => 'view',
            'target' => '_blank',
            'href'   => href_to($ctype['name'], '{slug}.html')
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->name, 'content', ['item_edit', $ctype['name'], '{id}']) . '?back=' . href_to($controller->name, 'content')
        ],
        [
            'title'   => LANG_RESTORE,
            'class'   => 'basket_remove',
            'href'    => href_to($ctype['name'], 'trash_remove', '{id}') . '?back=' . href_to($controller->name, 'content'),
            'confirm' => LANG_CP_CONTENT_ITEM_RESTORE_CONFIRM,
            'handler' => function ($row) {
                return $row['is_deleted'];
            }
        ],
        [
            'title'   => LANG_BASKET_DELETE,
            'class'   => 'basket_put',
            'href'    => href_to($ctype['name'], 'trash_put', '{id}') . '?back=' . href_to($controller->name, 'content'),
            'confirm' => LANG_CP_CONTENT_ITEM_BASKET_DELETE_CONFIRM,
            'handler' => function ($row) {
                return !$row['is_deleted'] && $row['is_approved'];
            }
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($ctype['name'], 'delete', '{id}') . '?back=' . href_to($controller->name, 'content'),
            'confirm' => LANG_CP_CONTENT_ITEM_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
