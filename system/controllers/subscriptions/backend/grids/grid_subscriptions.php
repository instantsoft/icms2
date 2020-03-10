<?php

function grid_subscriptions ($controller){

    cmsCore::loadAllControllersLanguages();

    $options = array(
        'is_sortable'   => false,
        'show_id'       => true,
        'is_selectable' => true,
        'order_by'      => 'subscribers_count',
        'order_to'      => 'desc'
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'class' => 'd-none d-lg-table-cell',
            'width' => 30
        ),
        'title' => array(
            'title'  => LANG_TITLE,
            'editable' => array(
                'table' => 'subscriptions'
            ),
            'filter' => 'like'
        ),
        'controller' => array(
            'title'  => LANG_EVENTS_LISTENER,
            'class' => 'd-none d-lg-table-cell',
            'width'  => 200,
            'filter' => 'like',
            'filter_select' => array(
                'items' => function($name){
                    $admin_model = cmsCore::getModel('admin');
                    $controllers = $admin_model->getInstalledControllers();
                    $items = array('' => '');
                    foreach($controllers as $controller){
                        $items[$controller['name']] = $controller['title'];
                    }
                    return $items;
                }
            ),
            'handler' => function($val, $row){
                return string_lang($val.'_CONTROLLER', $val);
            }
        ),
        'subject' => array(
            'title' => LANG_CP_SUBJECT,
            'class' => 'd-none d-lg-table-cell',
            'width' => 200
        ),
        'subscribers_count' => array(
            'title' => LANG_SBSCR_SUBSCRIBERS,
            'width' => 80
        )
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'view',
            'href'  => rel_to_href('{subject_url}')
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', array('{id}')),
            'confirm' => LANG_SBSCR_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
