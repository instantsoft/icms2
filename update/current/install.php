<?php
/**
 * 2.9.0 => 2.10
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->getRowsCount('activity_types', "controller = 'subscriptions' AND name = 'subscribe'")){
        $core->db->query("INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES (1, 'subscriptions', 'subscribe', 'Подписка на контент', 'подписывается на список %s');");
    }

    $core->db->query("ALTER TABLE `{#}controllers` CHANGE `files` `files` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Список файлов контроллера (для стороних компонентов)';");
    $core->db->query("ALTER TABLE `{#}widgets` CHANGE `files` `files` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Список файлов виджета (для стороних виджетов)';");

    if(!$core->db->getRowsCount('{users}_tabs', "controller = 'users' AND name = 'subscribers'", 1)){
        $admin->model->insert('{users}_tabs', array(
            'controller' => 'users',
            'title'      => 'Подписчики',
            'name'       => 'subscribers',
            'ordering'   => 2,
            'is_active'  => 1
        ));
    }

    if(!$core->db->getRowsCount('{users}_tabs', "controller = 'subscriptions' AND name = 'subscriptions'", 1)){
        $admin->model->insert('{users}_tabs', array(
            'controller' => 'subscriptions',
            'title'      => 'Подписки',
            'name'       => 'subscriptions',
            'ordering'   => 2,
            'is_active'  => 1
        ));
    }

    if(!$core->db->isFieldExists('tags', 'tag_title')){
        $core->db->query("ALTER TABLE `{#}tags` ADD `tag_title` VARCHAR(300) NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('tags', 'tag_desc')){
        $core->db->query("ALTER TABLE `{#}tags` ADD `tag_desc` VARCHAR(300) NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('tags', 'tag_h1')){
        $core->db->query("ALTER TABLE `{#}tags` ADD `tag_h1` VARCHAR(300) NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('{users}', 'subscribers_count')){
        $core->db->query("ALTER TABLE `{users}` ADD `subscribers_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `friends_count`;");
    }

    if($core->db->isFieldExists('sessions_online', 'session_id')){
        $core->db->query("TRUNCATE `{#}sessions_online`");
        $core->db->query("ALTER TABLE `{#}sessions_online` DROP `session_id`");
    }

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'subscriptions' AND hook = 'delete_expired_unconfirmed'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`) VALUES ('Удаляет просроченные неподтвержденные подписки гостей', 'subscriptions', 'delete_expired_unconfirmed', 1440, 1, '2018-03-21 00:03:00', 1);");
    }

    if(!$core->db->getRowsCount('controllers', "name = 'subscriptions'")){
        $core->db->query("INSERT INTO `{#}controllers` (`title`, `name`, `slug`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`, `files`, `addon_id`) VALUES ('Подписки', 'subscriptions', NULL, 1, '---\nguest_email_confirmation: 1\nneed_auth: null\nverify_exp: 24\nupdate_user_rating: 1\nrating_value: 1\nadmin_email:\nlimit: 20\n', 'InstantCMS Team', 'http://www.instantcms.ru', '2.0', 1, NULL, NULL, NULL);");
    }

    if(!$core->db->getRowsCount('widgets', "controller = 'groups' AND name = 'list'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES ('groups', 'list', 'Список групп', 'InstantCMS Team', 'http://www.instantcms.ru', '2.0', NULL);");
    }

    if(!$core->db->getRowsCount('widgets', "controller = 'subscriptions' AND name = 'button'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES ('subscriptions', 'button', 'Кнопки подписки', 'InstantCMS Team', 'http://www.instantcms.ru', '2.0', NULL);");
    }

    if(!$core->db->getRowsCount('widgets', "controller = 'auth' AND name = 'register'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES ('auth', 'register', 'Форма регистрации', 'InstantCMS Team', 'http://www.instantcms.ru', '2.0', NULL);");
    }

    $core->db->query("UPDATE `{#}controllers` SET `options` = NULL WHERE `name` = 'admin'");

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Индексы //////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    $remove_table_indexes = array(
        'sessions_online' => array(
            'session_id', 'user_id'
        )
    );
    $add_table_uq_indexes = array(
        'sessions_online' => array(
            'user_id' => array('user_id')
        )
    );

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
