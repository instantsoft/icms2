<?php

function grid_logs($controller){

    $options = array(
        'order_by'      => 'date_pub',
        'is_pagination' => true,
        'is_filter'     => false,
        'order_to'      => 'desc'
    );

    $columns = array(
        'date_pub' => array(
            'title' => LANG_DATE,
            'width' => 110,
            'handler' => function($value, $item){
                return html_date($value, true);
            }
        ),
        'action' => array(
            'title' => LANG_MODERATION_STATUS,
            'width' => 190,
            'handler' => function($value, $item){
                return html_bool_span(string_lang('LANG_MODERATION_ACTION_'.$value), ($value == 2));
            }
        ),
        'target_controller' => array(
            'title' => LANG_CP_SCHEDULER_TASK_CONTROLLER,
            'width' => 130,
            'handler' => function($value, $item){
                return $item['controller_title'];
            }
        ),
        'target_subject' => array(
            'title' => LANG_CP_SUBJECT,
            'width' => 130,
            'handler' => function($value, $item) use ($controller){
                return '<a href="'.href_to($controller->root_url, 'logs', array($item['target_controller'], $item['target_subject'])).'">'.$item['subject_title'].'</a>';
            }
        ),
        'data' => array(
            'title' => LANG_MODERATION_SUBJECT_ITEM,
            'handler' => function($value, $item){
                if(isset($item['data']['url'])){
                    $url = rel_to_href($item['data']['url']);
                } else {
                    $url = href_to($item['target_subject'], $item['data']['slug'].'.html');
                }
                return '<a target="_blank" href="'.$url.'">'.$item['data']['title'].'</a>';
            }
        ),
        'date_expired' => array(
            'title' => LANG_MODERATION_DEL_TIME,
            'width' => 150,
            'handler' => function($value, $item) {
                if($value){
                    $r = string_date_age_max($value); $expired = false;
                    if(time() - strtotime($value) > 0){
                        $r = '- '.$r; $expired = true;
                    }
                    return html_bool_span($r, !$expired);
                } else {
                    return '-';
                }
            }
        ),
        'moderator_id' => array(
            'title' => LANG_MODERATOR,
            'width' => 150,
            'order_by' => 'u.nickname',
            'handler' => function($value, $item) {
                if($item['user_nickname']){
                    return '<a href="'.href_to('users', $item['moderator_id']).'">'.$item['user_nickname'].'</a>';
                } else {
                    return LANG_CP_SCHEDULER;
                }
            }
        )
    );

    $actions = array();

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

