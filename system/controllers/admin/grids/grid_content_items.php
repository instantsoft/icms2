<?php

function grid_content_items($controller, $ctype_name=false){

    $options = array(
        'is_auto_init' => false,
        'is_sortable' => true,
        'is_filter' => true,
        'is_pagination' => true,
        'is_columns_settings' => true,
        'is_draggable' => false,
        'is_selectable' => true,
        'order_by' => 'id',
        'order_to' => 'desc',
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'class' => 'd-none d-lg-table-cell',
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
            'class' => 'd-none d-lg-table-cell',
            'width' => 110,
            'handler' => function($value, $item){
                if($item['is_deleted']){
                    return '<span rel="set_class" data-class="is_deleted">'.html_date($value, true).'</span>';
                }
                return html_date($value, true);
            }
        ),
        'is_approved' => array(
            'title' => LANG_MODERATION,
            'class' => 'd-none d-sm-table-cell',
            'width' => 150,
            'handler' => function($value, $item) use ($controller, $ctype_name){
                if($item['is_deleted']){
                    $string = '<a href="'.href_to($controller->name, 'controllers', array('edit', 'moderation', 'logs', 'content', $ctype_name, $item['id'])).'">';
                    if($item['trash_date_expired']){
                        $expired = ((time() - strtotime($item['trash_date_expired'])) > 0) ? true : false;
                        $string .= sprintf(LANG_MODERATION_IN_TRASH_TIME, ($expired ? '-' : '').string_date_age_max($item['trash_date_expired']));
                    } else {
                        $string .= LANG_MODERATION_IN_TRASH;
                    }
                    $string .= '</a>';
                    return $string;
                }
                if($item['is_approved']){
                    if($item['approved_by']){
                        return html_bool_span(LANG_MODERATION_SUCCESS, true);
                    } else {
                        return html_bool_span(LANG_MODERATION_NOT_NEEDED, true);
                    }
                } else {
                    if(!empty($item['is_draft'])){
                        return html_bool_span(LANG_CONTENT_DRAFT_NOTICE, false);
                    }
                    return html_bool_span(LANG_CONTENT_NOT_APPROVED, false);
                }
            }
        ),
        'is_pub' => array(
            'title' => LANG_ON,
            'class' => 'd-none d-sm-table-cell',
            'width' => 40,
            'flag' => true,
			'flag_toggle' => href_to($controller->name, 'content', array('item_toggle', $ctype_name, '{id}'))
        ),
        'user_nickname' => array(
            'title' => LANG_AUTHOR,
            'class' => 'd-none d-lg-table-cell',
            'href' => href_to('users', '{user_id}'),
            'order_by' => 'u.nickname',
        ),
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'view',
            'href'  => href_to($ctype_name, '{slug}.html')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($ctype_name, 'edit',  '{id}') . '?back=' . href_to($controller->name, 'content')
        ),
        array(
            'title' => LANG_RESTORE,
            'class' => 'basket_remove',
            'href'  => href_to($ctype_name, 'trash_remove',  '{id}') . '?back=' . href_to($controller->name, 'content'),
            'confirm' => LANG_CP_CONTENT_ITEM_RESTORE_CONFIRM,
            'handler' => function($row){
                return $row['is_deleted'];
            }
        ),
        array(
            'title' => LANG_BASKET_DELETE,
            'class' => 'basket_put',
            'href'  => href_to($ctype_name, 'trash_put',  '{id}') . '?back=' . href_to($controller->name, 'content'),
            'confirm' => LANG_CP_CONTENT_ITEM_BASKET_DELETE_CONFIRM,
            'handler' => function($row){
                return !$row['is_deleted'];
            }
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

