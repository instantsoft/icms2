<?php

function grid_content_items($controller, $ctype_name=false){

    $options = array(
        'is_auto_init' => false,
        'is_sortable' => true,
        'is_filter' => true,
        'is_pagination' => true,
        'is_draggable' => false,
        'is_selectable' => true, 
        'order_by' => 'id',
        'order_to' => 'desc',
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
            'filter' => 'exact'
        ),
        'title' => array(
            'title' => LANG_TITLE,
            'href' => href_to($ctype_name, 'edit',  '{id}') . '?back=' . href_to($controller->name, 'content'),
            'filter' => 'like'
        ),
        'date_pub' => array(
            'title' => LANG_DATE,
            'width' => 80,
            'handler' => function($value, $item){
                return html_date($value);
            }
        ),
        'is_pub' => array(
            'title' => LANG_ON,
            'width' => 40,
            'flag' => true,
			'flag_toggle' => href_to($controller->name, 'content', array('item_toggle', $ctype_name, '{id}'))
        ),
        'user_nickname' => array(
            'title' => LANG_AUTHOR,            
            'href' => href_to('users', '{user_id}'),
            'order_by' => 'u.nickname', 
        ),
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'view',
            'href' => href_to($ctype_name, '{slug}.html')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($ctype_name, 'edit',  '{id}') . '?back=' . href_to($controller->name, 'content')
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($ctype_name, 'delete',  '{id}') . '?back=' . href_to($controller->name, 'content'),
            'confirm' => LANG_CP_CONTENT_ITEM_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}

