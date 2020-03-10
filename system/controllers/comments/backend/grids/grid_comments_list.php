<?php

function grid_comments_list($controller){

    $options = array(
        'is_sortable'   => true,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'is_selectable' => true,
        'order_by'      => 'date_pub',
        'order_to'      => 'desc',
        'show_id'       => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'class' => 'd-none d-lg-table-cell',
            'width' => 30
        ),
        'date_pub' => array(
            'title'   => LANG_DATE,
            'class' => 'd-none d-lg-table-cell',
            'width'   => 110,
            'handler' => function($value, $item) {
                return html_date($value, true);
            },
            'filter'   => 'date'
        ),
        'target_id' => array(
            'title'   => LANG_COMMENTS_TEXT,
            'handler' => function($value, $row) use($controller) {
                return '<a title="'.LANG_COMMENTS_EDIT_TEXT.'" class="ajax-modal comment_text_edit" href="'.href_to($controller->root_url, 'text_edit', array($row['id'])).'">'.string_short($row['content_html'], 350).'</a>';
            }
        ),
        'user_id' => array(
            'title'   => LANG_AUTHOR,
            'width'   => 180,
            'handler' => function($value, $row) {
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
        ),
        'author_url' => array(
            'title'   => LANG_COMMENTS_IP,
            'class'   => 'd-none d-lg-table-cell',
            'width'   => 120,
            'filter'  => 'like',
            'handler' => function($value) {
                if ($value) {
                    return '<a href="#" class="ajaxlink filter_ip" data-toggle="tooltip" data-placement="top" title="' . LANG_CP_USER_FIND_BYIP . '">' . $value . '</a> <a data-toggle="tooltip" data-placement="top" class="view_target" href="https://apps.db.ripe.net/search/query.html?searchtext=' . $value . '#resultsAnchor" target="_blank" title="' . LANG_CP_USER_RIPE_SEARCH . '" rel="noopener noreferrer"></a>';
                }
                return '';
            }
        ),
        'rating' => array(
            'title'   => LANG_RATING,
            'class'   => 'd-none d-lg-table-cell',
            'width'   => 50,
            'handler' => function($value, $row) {
                return '<span class="' . html_signed_class($value) . '">' . html_signed_num($value) . '</span>';
            },
            'filter' => 'exact'
        ),
        'is_deleted' => array(
            'title'       => LANG_COMMENTS_IS_DELETED,
            'class'       => 'd-none d-lg-table-cell',
            'flag'        => 'flag_lock',
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'comments', 'is_deleted', 1)),
            'width'       => 50,
            'filter'      => 'exact'
        ),
        'is_approved' => array(
            'title'  => LANG_MODERATION,
            'flag'   => true,
            'width'  => 50,
            'filter' => 'exact',
            'handler' => function($value, $item){
                if(!$item['is_approved']){
                    return '<div class="flag_trigger flag_off"><span><a class="approve_comment" title="'.LANG_COMMENTS_APPROVE.'" href="#" data-approve-url="'.href_to('comments', 'approve').'?id='.$item['id'].'"></a></span></div>';
                }
                return '<div class="flag_trigger flag_on"></div>';
            }
        ),
        'is_private' => array(
            'title'  => LANG_COMMENTS_IS_PRIVATE,
            'class'  => 'd-none d-lg-table-cell',
            'flag'   => true,
            'width'  => 50,
            'filter' => 'exact'
        ),
    );

    $actions = array(
        array(
            'title' => LANG_COMMENTS_VIEW,
            'class' => 'view',
            'href'  => rel_to_href('{target_url}').'#comment_{id}'
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'comments_delete', array('{id}')),
            'confirm' => LANG_COMMENTS_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
