<?php
/**
 * 2.11.0 => 2.11.1
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('content_types', 'is_enabled')){
        $core->db->query("ALTER TABLE `{#}content_types` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `ordering`;");
    }

    if(!$core->db->isFieldExists('content_datasets', 'seo_h1')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `seo_h1` VARCHAR(256) NULL DEFAULT NULL AFTER `seo_title`;");
    }

    $content_model = cmsCore::getModel('content');

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'users' AND hook = 'sessionclean'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`, `is_new`) VALUES ('Удаляет устаревшие сессии', 'users', 'sessionclean', 10, NULL, NULL, 1, 1);");
    }

    $ctypes = $content_model->getContentTypes();

	foreach($ctypes as $ctype){

        $table_name = $content_model->table_prefix.$ctype['name'].'_fields';

        if(!$core->db->isFieldExists($table_name, 'groups_add')){
            $core->db->query("ALTER TABLE `{#}{$table_name}` ADD `groups_add` TEXT NULL DEFAULT NULL AFTER `groups_read`;");
            $core->db->query("UPDATE `{#}{$table_name}` SET `groups_add`= `groups_edit`;");
        }

        if(!$core->db->isFieldExists($table_name, 'is_enabled')){
            $core->db->query("ALTER TABLE `{#}{$table_name}` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `ordering`;");
        }

	}

    if(!$core->db->isFieldExists("groups_fields", 'groups_add')){
        $core->db->query("ALTER TABLE `{#}groups_fields` ADD `groups_add` TEXT NULL DEFAULT NULL AFTER `groups_read`;");
        $core->db->query("UPDATE `{#}groups_fields` SET `groups_add`= `groups_edit`;");
    }
    if(!$core->db->isFieldExists('groups_fields', 'is_enabled')){
        $core->db->query("ALTER TABLE `{#}groups_fields` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `ordering`;");
    }
    if(!$core->db->isFieldExists("{users}_fields", 'groups_add')){
        $core->db->query("ALTER TABLE `{users}_fields` ADD `groups_add` TEXT NULL DEFAULT NULL AFTER `groups_read`;");
        $core->db->query("UPDATE `{users}_fields` SET `groups_add`= `groups_edit`;");
    }
    if(!$core->db->isFieldExists('{users}_fields', 'is_enabled')){
        $core->db->query("ALTER TABLE `{users}_fields` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `ordering`;");
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////


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
            if($controller == 'auth'){
                $options['is_site_only_auth_users'] = cmsConfig::get('is_site_only_auth_users');
                $options['guests_allow_controllers'] = array('auth', 'geo');
            }
            cmsCore::getModel('content')->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}
