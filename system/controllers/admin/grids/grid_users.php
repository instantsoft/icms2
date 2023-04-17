<?php

function grid_users($controller){

    $options = [
        'advanced_filter' => $controller->cms_template->href_to('users', ['filter']),
        'is_sortable'     => true,
        'is_filter'       => true,
        'is_pagination'   => true,
        'is_draggable'    => false,
        'order_by'        => 'id',
        'order_to'        => 'asc',
        'show_id'         => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'nickname' => [
            'title'           => LANG_NICKNAME,
            'href'            => href_to($controller->name, 'users', ['edit', '{id}']),
            'filter'          => 'like',
            'tooltip_handler' => function ($user) {
                return $user['is_admin'] ? LANG_USER_IS_ADMIN : '';
            },
            'handler' => function ($value, $user) {
                return '<span class="d-flex align-items-center"><span class="icms-user-avatar mr-2 '.($user['is_online'] ? 'peer_online' : 'peer_no_online').'">'.
                    html_avatar_image($user['avatar'], 'micro', $user['nickname'])
                .'</span>'.($user['is_admin'] ? '<b>'.$value.'</b>' : $value).'</span>';
            }
        ],
        'email' => [
            'title'      => LANG_EMAIL,
            'switchable' => true,
            'filter'     => 'like'
        ],
        'ip' => [
            'title'      => LANG_USERS_PROFILE_LAST_IP,
            'width'      => 130,
            'class'      => 'd-none d-xxl-table-cell',
            'filter'     => 'like',
            'switchable' => true,
            'handler'    => function ($value) {
                if (!$value) {
                    return '';
                } elseif (strpos($value, '127.') === 0) {
                    return $value;
                }
                $location = string_ip_to_location($value, true);
                return '<div class="d-flex justify-content-between align-items-center"><span>' . $value . '</span>&nbsp;<span>' . (!empty($location['code']) ? '<span class="small">' . $location['code'] . '</span>&nbsp;' : '') . '<a class="view_target text-decoration-none" href="https://apps.db.ripe.net/db-web-ui/query?searchtext=' . $value . '" target="_blank" rel="noopener noreferrer" title="' . LANG_CP_USER_RIPE_SEARCH . '">'. html_svg_icon('solid', 'globe', 16, false).'</a></span></div>';
            }
        ],
        'date_reg' => [
            'title'           => LANG_REGISTRATION,
            'class'           => 'd-none d-md-table-cell',
            'filter'          => 'date',
            'switchable'      => true,
            'tooltip_handler' => function ($user) {
                return $user['is_online'] ? LANG_ONLINE : LANG_USERS_PROFILE_LOGDATE . ' ' . string_date_age_max($user['date_log'], true);
            },
            'handler' => function ($date, $user) {
                return html_date($date);
            }
        ],
        'karma' => [
            'title'      => LANG_KARMA,
            'class'      => 'd-none d-xxl-table-cell',
            'width'      => 60,
            'filter'     => 'exact',
            'switchable' => true,
            'handler'    => function ($value) {
                return '<span class="' . html_signed_class($value) . '">' . html_signed_num($value) . '</span>';
            }
        ],
        'rating' => [
            'title'      => LANG_RATING,
            'class'      => 'd-none d-xxl-table-cell',
            'width'      => 60,
            'switchable' => true,
            'filter'     => 'exact'
        ],
        'is_locked' => [
            'title'           => LANG_CP_USER_LOCKED,
            'class'           => 'd-none d-sm-table-cell',
            'flag'            => 'flag_lock',
            'width'           => 24,
            'switchable'      => true,
            'tooltip_handler' => function ($user) {
                if (!$user['is_locked']) {
                    return '';
                }
                return ($user['lock_reason'] ? $user['lock_reason'].', ' : '') . LANG_TO . ' ' . strip_tags(html_date($user['lock_until']));
            }
        ],
        'is_deleted' => [
            'title'      => LANG_ADMIN_IS_DELETED,
            'class'      => 'd-none d-sm-table-cell',
            'width'      => 24,
            'switchable' => true,
            'handler'    => function ($value, $user) {
                return html_bool_span(($value ? LANG_YES : LANG_NO), !$value);
            }
        ]
    ];

    $actions = [
        [
            'title'  => LANG_PROFILE,
            'target' => '_blank',
            'class'  => 'view',
            'href'   => href_to('users', '{id}')
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to('users', '{id}', ['edit']) . '?back=' . href_to($controller->name, 'users')
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->name, 'users', ['delete', '{id}']),
            'confirm' => LANG_CP_USER_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
