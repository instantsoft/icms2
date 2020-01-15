<?php
/**
 * 2.12.2 => 2.13.0
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('{users}', '2fa')){
        $core->db->query("ALTER TABLE `{users}` ADD `2fa` VARCHAR(32) NULL DEFAULT NULL AFTER `ip`");
    }

    if(!$core->db->isFieldExists('widgets', 'image_hint_path')){
        $core->db->query("ALTER TABLE `{#}widgets` ADD `image_hint_path` VARCHAR(100) NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('widgets_bind', 'is_cacheable')){
        $core->db->query("ALTER TABLE `{#}widgets_bind` ADD `is_cacheable` TINYINT(1) UNSIGNED NULL DEFAULT '1'");
    }

    if(!$core->db->isFieldExists('scheduler_tasks', 'ordering')){
        $core->db->query("ALTER TABLE `{#}scheduler_tasks` ADD `ordering` INT(11) UNSIGNED NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('{users}_groups', 'ordering')){
        $core->db->query("ALTER TABLE `{users}_groups` ADD `ordering` INT(11) UNSIGNED NULL DEFAULT '1'");
        $core->db->query("UPDATE `{users}_groups` SET `ordering` = `id` WHERE 1");
        $core->db->query("ALTER TABLE `{users}_groups` ADD KEY `ordering` (`ordering`);");
    }

    cmsUser::deleteUPSlist('admin.grid_filter.set_scheduler');

    save_controller_options(['messages']);

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    add_perms(array(
        'users' => array(
            'change_email'
        )
    ), 'flag');

    add_perms(array(
        'content' => array(
            'limit24'
        ),
        'users' => array(
            'change_email_period'
        )
    ), 'number');

    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Индексы //////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    $remove_table_indexes = array();
    $add_table_uq_indexes = array();

    if($remove_table_indexes){
        foreach ($remove_table_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name) {
                $core->db->dropIndex($table, $index_name);
            }
        }
    }
    if($add_table_uq_indexes){
        foreach ($add_table_uq_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($table, $fields, $index_name, 'UNIQUE');
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

    foreach ($controllers as $controller) {
        $controller_root_path = cmsConfig::get('root_path').'system/controllers/'.$controller.'/';
        $form_file = $controller_root_path.'backend/forms/form_options.php';
        $form_name = $controller.'options';
        cmsCore::loadControllerLanguage($controller);
        cmsCore::includeFile('system/controllers/'.$controller.'/model.php');
        $form = cmsForm::getForm($form_file, $form_name, false);
        if ($form) {
            $options = $form->parse(new cmsRequest(cmsController::loadOptions($controller)));
            cmsCore::getModel('content')->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}
