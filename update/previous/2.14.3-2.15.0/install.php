<?php
/**
 * 2.14.3 => 2.15.0
 */
function install_package(){

    $core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('images_presets', 'convert_format')){
        $core->db->query("ALTER TABLE `{#}images_presets` ADD `convert_format` CHAR(4) NULL DEFAULT NULL;");
    }

    if(!$core->db->isFieldExists('images_presets', 'gif_to_gif')){
        $core->db->query("ALTER TABLE `{#}images_presets` ADD `gif_to_gif` TINYINT(1) UNSIGNED NULL DEFAULT '1';");
    }

    if(!$core->db->getRowsCount('widgets', "`controller` = 'content' AND `name` = 'author'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`) VALUES ('content', 'author', 'Автор записи', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL);");
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    add_perms([
        'content' => [
            'edit_times', 'delete_times'
        ]
    ], 'number');

    add_perms([
        'users' => [
            'wall_add'
        ]
    ], 'flag');

    add_perms([
        'users' => [
            'wall_delete'
        ]
    ], 'list', 'own,all');

    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Индексы //////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    $remove_table_indexes = [];
    $add_table_indexes = [];

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

    save_controller_options(['users']);

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
