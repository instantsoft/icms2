<?php
/**
 * 2.14.2 => 2.14.3
 */
function install_package(){

    $core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('{users}_contacts', 'new_messages')){
        $core->db->query("ALTER TABLE `{users}_contacts` CHANGE `messages` `new_messages` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Кол-во новых сообщений';");
    }

    if(!$core->db->isFieldExists('widgets_pages', 'body_css')){
        $core->db->query("ALTER TABLE `{#}widgets_pages` ADD `body_css` VARCHAR(100) NULL DEFAULT NULL;");
    }

    $widgets_bind = [];
    $wbinds = $admin->model->orderBy('i.page_id, i.position, i.ordering')->
                    get('widgets_bind_pages') ?: [];
    foreach ($wbinds as $wbind) {
        $widgets_bind[$wbind['template']][$wbind['position']][] = $wbind;
    }
    foreach ($widgets_bind as $tpl => $positions) {
        foreach ($positions as $wbs) {
            foreach ($wbs as $ordering => $wb) {
                $admin->model->update('widgets_bind_pages', $wb['id'], [
                    'ordering' => $ordering
                ]);
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    add_perms([
        'comments' => [
            'times'
        ]
    ], 'number');

    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Индексы //////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    $remove_table_indexes = [
        '{users}_contacts' => [
            'user_id'
        ],
        '{users}_messages' => [
            'date_delete', 'from_id', 'to_id'
        ]
    ];
    $add_table_indexes = [
        '{users}_contacts' => [
            'user_id' => ['user_id', 'date_last_msg']
        ],
        '{users}_messages' => [
            'to_id' => ['to_id', 'from_id', 'is_deleted']
        ]
    ];

    if($remove_table_indexes){
        foreach ($remove_table_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name) {
                $core->db->dropIndex($table, $index_name);
            }
        }
    }
    if($add_table_indexes){
        foreach ($add_table_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($table, $fields, $index_name);
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Обновляем события ///////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    $diff_events = $admin->getEventsDifferences();

    if($diff_events['added']){
        foreach ($diff_events['added'] as $controller => $events) {
            foreach ($events as $event){
                $admin->model->addEvent($controller, $event);
            }
        }
    }

    if($diff_events['deleted']){
        foreach ($diff_events['deleted'] as $controller => $events) {
            foreach ($events as $event){
                $admin->model->deleteEvent($controller, $event);
            }
        }
    }

    return true;
}

// добавление прав доступа
function add_perms($data, $type, $options = null) {

    $model = new cmsModel();

    foreach ($data as $controller => $names) {

        foreach ($names as $name) {

            if(!$model->db->getRowsCount('perms_rules', "controller = '{$controller}' AND name = '{$name}'", 1)){
                $model->insert('perms_rules', array(
                    'controller' => $controller,
                    'name'       => $name,
                    'type'       => $type,
                    'options'    => $options
                ));
            }

        }

    }

}

// настройки контроллеров для пересохранения
function save_controller_options($controllers) {

    $model = new cmsModel();

    foreach ($controllers as $controller) {
        $controller_root_path = cmsConfig::get('root_path').'system/controllers/'.$controller.'/';
        $form_file = $controller_root_path.'backend/forms/form_options.php';
        $form_name = $controller.'options';
        cmsCore::loadControllerLanguage($controller);
        cmsCore::includeFile('system/controllers/'.$controller.'/model.php');
        $form = cmsForm::getForm($form_file, $form_name, false);
        if ($form) {
            $options = $form->parse(new cmsRequest(cmsController::loadOptions($controller)));
            $model->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}
