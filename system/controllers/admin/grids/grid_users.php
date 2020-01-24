<?php

function grid_users($controller){

    $options = array(
        'is_auto_init' => false,
        'is_sortable' => true,
        'is_filter' => true,
        'is_pagination' => true,
        'is_draggable' => false,
        'order_by' => 'id',
        'order_to' => 'asc',
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'class' => 'd-none d-lg-table-cell',
            'width' => 30,
            'filter' => 'exact'
        ),
        'nickname' => array(
            'title'   => LANG_NICKNAME,
            'href'    => href_to($controller->name, 'users', array('edit', '{id}')),
            'filter'  => 'like',
            'handler' => function($nickname, $user) {
                if ($user['is_admin']) {
                    $nickname = '<b data-toggle="tooltip" data-placement="top" title="' . LANG_USER_IS_ADMIN . '">' . $nickname . '</b>';
                }
                return $nickname;
            }
        ),
        'email' => array(
            'title'  => LANG_EMAIL,
            'width'  => 200,
            'filter' => 'like'
        ),
        'ip' => array(
            'title' => LANG_USERS_PROFILE_LAST_IP,
            'width' => 130,
            'class' => 'd-none d-xxl-table-cell',
            'filter' => 'like',
            'handler' => function($value){
                if(!$value){
                    return '';
                } elseif(strpos($value, '127.') === 0){
                    return $value;
                }
                return '<a href="#" class="ajaxlink filter_ip" data-toggle="tooltip" data-placement="top" title="'.LANG_CP_USER_FIND_BYIP.'">'.$value.'</a> <a class="view_target" data-toggle="tooltip" data-placement="top" href="https://apps.db.ripe.net/db-web-ui/query?searchtext='.$value.'#resultsAnchor" target="_blank" rel="noopener noreferrer" title="'.LANG_CP_USER_RIPE_SEARCH.'"><i class="icon-globe icons"></i></a>';
            }
        ),
        'date_reg' => array(
            'title' => LANG_REGISTRATION,
            'class' => 'd-none d-md-table-cell',
            'width' => 80,
            'filter' => 'date',
            'handler' => function($date, $user){
                $ld = $user['is_online'] ? LANG_ONLINE : LANG_USERS_PROFILE_LOGDATE.' '.string_date_age_max($user['date_log'], true);
                return '<span data-toggle="tooltip" data-placement="top" title="'.$ld.'">'.html_date($date).'</span>';
            }
        ),
        'karma' => array(
            'title' => LANG_KARMA,
            'class' => 'd-none d-xxl-table-cell',
            'width' => 60,
            'filter' => 'exact',
            'handler' => function($value){
                return '<span class="'.  html_signed_class($value).'">'.html_signed_num($value).'</span>';
            }
        ),
        'rating' => array(
            'title' => LANG_RATING,
            'class' => 'd-none d-xxl-table-cell',
            'width' => 60,
            'filter' => 'exact'
        ),
        'is_locked' => array(
            'title' => LANG_CP_USER_LOCKED,
            'class' => 'd-none d-sm-table-cell',
            'flag' => 'flag_lock',
            'width' => 24,
            'handler' => function($value, $user){
                $title = $user['is_locked'] ? ($user['lock_reason'] ? $user['lock_reason'] : LANG_TO.' '.strip_tags(html_date($user['lock_until']))) : '';
                return '<div data-toggle="tooltip" data-placement="top" title="'.$title.'">'.$value.'</div>';
            }
        ),
        'is_deleted' => array(
            'title' => LANG_ADMIN_IS_DELETED,
            'class' => 'd-none d-sm-table-cell',
            'width' => 24,
            'handler' => function($value, $user){
                return html_bool_span(($value ? LANG_YES : LANG_NO), !$value);
            }
        )
    );

    $actions = array(
        array(
            'title' => LANG_PROFILE,
            'class' => 'view',
            'href' => href_to('users', '{id}')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to('users', '{id}', array('edit')) . '?back=' . href_to($controller->name, 'users')
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->name, 'users', array('delete', '{id}')),
            'confirm' => LANG_CP_USER_DELETE_CONFIRM
        ),
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

