<?php
/**
 * 2.10.1 => 2.11.0
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');
    $content_model = cmsCore::getModel('content');

    $ctypes = $content_model->getContentTypes();

	foreach($ctypes as $ctype){

        $table_name = $content_model->table_prefix . $ctype['name'];
        $table_name_cat = $table_name.'_cats';

        if(!$core->db->isFieldExists($table_name, 'template')){
            $content_model->db->query("ALTER TABLE `{#}{$table_name}` ADD `template` VARCHAR(150) NULL DEFAULT NULL AFTER `tags`");
        }

        if(!$core->db->isFieldExists($table_name_cat, 'is_hidden')){
            $content_model->db->query("ALTER TABLE `{#}{$table_name_cat}` ADD `is_hidden` TINYINT(1) UNSIGNED NULL DEFAULT NULL");
        }

        if(!$core->db->isFieldExists($table_name_cat, 'cover')){
            $content_model->db->query("ALTER TABLE `{#}{$table_name_cat}` ADD `cover` TEXT NULL DEFAULT NULL");
        }

        if(!$core->db->isFieldExists($table_name_cat, 'seo_h1')){
            $content_model->db->query("ALTER TABLE `{#}{$table_name_cat}` ADD `seo_h1` VARCHAR(256) NULL DEFAULT NULL AFTER `seo_title`");
        }

	}

    if(!$core->db->isFieldExists('widgets_bind', 'languages')){
        $core->db->query("ALTER TABLE `{#}widgets_bind` ADD `languages` VARCHAR(100) NULL DEFAULT NULL AFTER `template_layouts`;");
    }

    if(!$core->db->isFieldExists('scheduler_tasks', 'consistent_run')){
        $core->db->query("ALTER TABLE `{#}scheduler_tasks` ADD `consistent_run` TINYINT(1) UNSIGNED NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('menu_items', 'is_enabled')){
        $core->db->query("ALTER TABLE `{#}menu_items` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `parent_id`");
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

    save_controller_options(array('sitemap', 'comments', 'geo', 'auth'));

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
