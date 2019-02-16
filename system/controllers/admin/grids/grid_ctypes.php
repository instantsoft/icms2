<?php

function grid_ctypes($controller){

    $options = array(
        'is_sortable' => false,
        'is_filter' => false,
        'is_draggable' => true,
        'order_by' => 'ordering',
        'order_to' => 'asc'
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
            'filter' => 'exact'
        ),
        'title' => array(
            'title' => LANG_TITLE,
            'width' => 150,
            'href' => href_to($controller->name, 'ctypes', array('edit', '{id}')),
            'filter' => 'like'
        ),
        'name' => array(
            'title' => LANG_SYSTEM_NAME,
            'width' => 150,
            'filter' => 'like'
        ),
        'is_enabled' => array(
            'title' => LANG_IS_ENABLED,
			'flag' => true,
			'flag_toggle' => href_to($controller->name, 'toggle_item', array('{id}', 'content_types', 'is_enabled')),
            'width' => 80
        ),
        'url_pattern' => array(
            'title' => LANG_CP_URL_PATTERN,
            'width' => 200
        ),
        'is_cats' => array(
            'title' => LANG_CATEGORIES,
            'width' => 90,
            'handler' => function($value, $ctype){
                return '<div class="'.(!$value ? '' : 'positive').'">'.($value ? LANG_YES : LANG_NO).'</div>';
            }
        ),
        'is_folders' => array(
            'title' => LANG_CP_FOLDERS,
            'handler' => function($value, $ctype){
                return '<div class="'.(!$value ? '' : 'positive').'">'.($value ? LANG_YES : LANG_NO).'</div>';
            }
        )
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->name, 'ctypes', array('edit', '{id}'))
        ),
        array(
            'title' => LANG_CP_CTYPE_LABELS,
            'class' => 'labels',
            'href' => href_to($controller->name, 'ctypes', array('labels', '{id}'))
        ),
        array(
            'title' => LANG_CP_CTYPE_FIELDS,
            'class' => 'fields',
            'href' => href_to($controller->name, 'ctypes', array('fields', '{id}'))
        ),
        array(
            'title' => LANG_CP_CTYPE_PERMISSIONS,
            'class' => 'permissions',
            'href' => href_to($controller->name, 'ctypes', array('perms', '{id}'))
        ),
        array(
            'title' => LANG_CP_CTYPE_DATASETS,
            'class' => 'filter',
            'href' => href_to($controller->name, 'ctypes', array('datasets', '{id}'))
        ),
        array(
            'title' => LANG_MODERATORS,
            'class' => 'user',
            'href' => href_to($controller->name, 'ctypes', array('moderators', '{id}'))
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->name, 'ctypes', array('delete', '{id}')),
            'confirm' => LANG_CP_CTYPE_DELETE_CONFIRM,
            'handler' => function($row){
                return !$row['is_fixed'];
            }
        )
    );
    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
