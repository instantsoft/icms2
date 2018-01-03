<?php
/**
 * 2.8.2 => 2.9
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('groups', 'join_roles')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `join_roles` VARCHAR(1000) NULL DEFAULT NULL COMMENT 'Роли при вступлении в группу'");
    }

    if(!$core->db->isFieldExists('groups', 'is_approved')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `is_approved` TINYINT(1) NOT NULL DEFAULT '1'");
    }

    if(!$core->db->isFieldExists('groups', 'approved_by')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `approved_by` INT(11) NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('groups', 'date_approved')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `date_approved` TIMESTAMP NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('content_types', 'ordering')){
        $core->db->query("ALTER TABLE `{#}content_types` ADD `ordering` INT(11) NULL DEFAULT NULL AFTER `description`, ADD INDEX (`ordering`)");
    }

    if(!$core->db->isFieldExists('perms_rules', 'show_for_guest_group')){
        $core->db->query("ALTER TABLE `{#}perms_rules` ADD `show_for_guest_group` TINYINT(1) NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('content_datasets', 'list')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `list` TEXT NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('scheduler_tasks', 'is_strict_period')){
        $core->db->query("ALTER TABLE `{#}scheduler_tasks` ADD `is_strict_period` TINYINT(1) UNSIGNED NULL DEFAULT NULL AFTER `period`");
    }

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'queue' AND hook = 'run_queue'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_active`) VALUES ('Выполняет задачи системной очереди', 'queue', 'run_queue', '1', '1');");
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Добавляем модераторов комментариев //////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    $moderators = cmsPermissions::getRulesGroupMembers('comments', 'is_moderator');
    $model_moderation = cmsCore::getModel('moderation');
    if($moderators){
        foreach ($moderators as $moderator) {
            $model_moderation->addContentTypeModerator('comments', $moderator['id']);
        }

    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    add_perms(array(
        'auth' => array(
            'view_closed'
        )
    ), 'flag');

    add_perms(array(
        'content' => array(
            'view_list'
        )
    ), 'list', 'other,all');

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
