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
            'width' => 30,
            'filter' => 'exact'
        ),
        'nickname' => array(
            'title'   => LANG_NICKNAME,
            'href'    => href_to($controller->name, 'users', array('edit', '{id}')),
            'filter'  => 'like',
            'handler' => function($nickname, $user) {
                if ($user['is_admin']) {
                    $nickname = '<b class="tooltip" title="' . LANG_USER_IS_ADMIN . '">' . $nickname . '</b>';
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
            'filter' => 'like',
            'handler' => function($value){
                if(!$value){
                    return '';
                } elseif(strpos($value, '127.') === 0){
                    return $value;
                }
                return '<a href="#" class="ajaxlink filter_ip tooltip" title="'.LANG_CP_USER_FIND_BYIP.'">'.$value.'</a> <a class="view_target tooltip" href="https://apps.db.ripe.net/search/query.html?searchtext='.$value.'#resultsAnchor" target="_blank" title="'.LANG_CP_USER_RIPE_SEARCH.'"></a>';
            }
        ),
        'date_reg' => array(
            'title' => LANG_REGISTRATION,
            'width' => 80,
            'filter' => 'like',
            'handler' => function($date, $user){
                $ld = $user['is_online'] ? LANG_ONLINE : LANG_USERS_PROFILE_LOGDATE.' '.string_date_age_max($user['date_log'], true);
                return '<span class="tooltip" title="'.$ld.'">'.html_date($date).'</span>';
            }
        ),
        'karma' => array(
            'title' => LANG_KARMA,
            'width' => 60,
            'filter' => 'exact',
            'handler' => function($value){
                return '<span class="'.  html_signed_class($value).'">'.html_signed_num($value).'</span>';
            }
        ),
        'rating' => array(
            'title' => LANG_RATING,
            'width' => 60,
            'filter' => 'exact'
        ),
        'is_locked' => array(
            'title' => LANG_CP_USER_LOCKED,
            'flag' => 'flag_lock',
            'width' => 24,
            'handler' => function($value, $user){
                $title = $user['is_locked'] ? ($user['lock_reason'] ? $user['lock_reason'] : LANG_TO.' '.strip_tags(html_date($user['lock_until']))) : '';
                return '<div class="tooltip" title="'.$title.'">'.$value.'</div>';
            }
        ),
        'is_deleted' => array(
            'title' => LANG_ADMIN_IS_DELETED,
            'width' => 24,
            'handler' => function($value, $user){
                return '<div class="'.($value ? 'negative' : 'positive').'">'.($value ? LANG_YES : LANG_NO).'</div>';
            }
        )
    );

    $actions = array(
        array(
            'title' => LANG_PROFILE,
            'class' => 'view tooltip',
            'href' => href_to('users', '{id}')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit tooltip',
            'href'  => href_to('users', '{id}', array('edit')) . '?back=' . href_to($controller->name, 'users')
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete tooltip',
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

