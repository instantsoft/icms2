<?php

function grid_comments_list($controller) {

    $options = [
        'is_sortable'    => true,
        'is_filter'      => true,
        'is_pagination'  => true,
        'is_draggable'   => false,
        'is_selectable'  => true,
        'order_by'       => 'date_pub',
        'order_to'       => 'desc',
        'show_id'        => false,
        'select_actions' => [
            [
                'title'   => LANG_DELETE,
                'action'  => 'submit',
                'confirm' => LANG_DELETE_SELECTED_CONFIRM,
                'url'     => $controller->cms_template->href_to('comments_delete')
            ]
        ]
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'date_pub' => [
            'title'   => LANG_DATE,
            'handler' => function ($value, $item) {
                return html_date($value, true);
            },
            'filter' => 'range_date'
        ],
        'target_id' => [
            'title'    => LANG_COMMENTS_TEXT,
            'sortable' => false,
            'handler'  => function ($value, $row) use ($controller) {
                return '<a title="' . LANG_COMMENTS_EDIT_TEXT . '" class="ajax-modal comment_text_edit" href="' . href_to($controller->root_url, 'text_edit', [$row['id']]) . '">' . string_short($row['content_html'], 350) . '</a>';
            }
        ],
        'user_id' => [
            'title'      => LANG_AUTHOR,
            'switchable' => true,
            'handler'    => function ($value, $row) {
                if ($row['user_id']) {
                    $v = '<a target="_blank" href="' . href_to('users', $row['user_id']) . '">' . $row['user_nickname'] . '</a>';
                } else {
                    $v = '<span class="guest_name">' . $row['author_name'] . '</span>';
                    if (!empty($row['author_email'])) {
                        $v .= '<span>, ' . $row['author_email'] . '</span>';
                    }
                }
                return $v;
            }
        ],
        'author_ip' => [
            'title'      => LANG_COMMENTS_IP,
            'class'      => 'd-none d-lg-table-cell',
            'width'      => 120,
            'sortable'   => false,
            'filter'     => 'ip',
            'switchable' => true,
            'handler'    => function ($value) {
                if ($value) {
                    $value    = string_bintoip($value);
                    $location = string_ip_to_location($value, true);
                    return $value . (!empty($location['code']) ? '&nbsp;' . $location['code'] : '');
                }
                return '';
            }
        ],
        'rating' => [
            'title'      => LANG_RATING,
            'class'      => 'd-none d-lg-table-cell',
            'width'      => 50,
            'switchable' => true,
            'handler'    => function ($value, $row) {
                return '<span class="' . html_signed_class($value) . '">' . html_signed_num($value) . '</span>';
            },
            'filter' => 'exact'
        ],
        'is_deleted' => [
            'title'           => LANG_COMMENTS_IS_DELETED,
            'class'           => 'd-none d-lg-table-cell',
            'flag'            => 'flag_lock',
            'flag_toggle'     => href_to($controller->root_url, 'toggle_item', ['{id}', 'comments', 'is_deleted', 1]),
            'width'           => 50,
            'switchable'      => true,
            'filter'          => 'nn',
            'filter_checkbox' => LANG_YES
        ],
        'is_approved' => [
            'title'           => LANG_MODERATION,
            'flag'            => true,
            'flag_toggle'     => href_to('comments', 'approve') . '?id={id}',
            'flag_confirm'    => LANG_COMMENTS_APPROVE . '?',
            'width'           => 50,
            'switchable'      => true,
            'filter'          => 'zero',
            'filter_checkbox' => LANG_NO
        ],
        'is_private' => [
            'title'      => LANG_COMMENTS_IS_PRIVATE,
            'class'      => 'd-none d-lg-table-cell',
            'switchable' => true,
            'flag'       => true,
            'width'      => 50
        ]
    ];

    $actions = [
        [
            'title'  => LANG_COMMENTS_VIEW,
            'class'  => 'view',
            'target' => '_blank',
            'href'   => rel_to_href('{target_url}') . '#comment_{id}'
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'comments_delete', ['{id}']),
            'confirm' => LANG_COMMENTS_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
