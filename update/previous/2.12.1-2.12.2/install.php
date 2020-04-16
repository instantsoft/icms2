<?php
/**
 * 2.12.1 => 2.12.2
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    $core->db->query("DELETE FROM `{#}controllers` WHERE `name` = 'markitup'");

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'content' AND hook = 'publication_notify'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`, `is_new`) VALUES ('Рассылает уведомления об окончании публикации', 'content', 'publication_notify', 1440, 1, DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:05'), 1, 1);");
    }

    save_controller_options(['photos', 'wall', 'comments']);

    if(!$core->db->getRowsCount('controllers', "name = 'wysiwygs'")){
        $core->db->query("INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES ('Wysiwyg редакторы', 'wysiwygs', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);");
    }

    $core->db->query("ALTER TABLE `{#}uploaded_files` CHANGE `type` `type` VARCHAR(32) NULL DEFAULT 'file' COMMENT 'Тип файла';");

    if(!$core->db->isFieldExists('images_presets', 'gamma_correct')){
        $core->db->query("ALTER TABLE `{#}images_presets` ADD `gamma_correct` TINYINT(1) UNSIGNED NULL DEFAULT NULL;");
    }

    if(!$core->db->isFieldExists('images_presets', 'crop_position')){
        $core->db->query("ALTER TABLE `{#}images_presets` ADD `crop_position` TINYINT(1) UNSIGNED NULL DEFAULT '2';");
    }

    if(!$core->db->isFieldExists('images_presets', 'allow_enlarge')){
        $core->db->query("ALTER TABLE `{#}images_presets` ADD `allow_enlarge` TINYINT(1) UNSIGNED NULL DEFAULT NULL;");
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    add_perms(array(
        'content' => array(
            'limit24'
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
