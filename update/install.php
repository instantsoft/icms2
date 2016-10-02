<?php

function install_package(){

	$core = cmsCore::getInstance();
    $content_model = cmsCore::getModel('content');

    if(!$core->db->getRowsCount('controllers', "name = 'redirect'")){
        $core->db->query("INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`) VALUES ('Редиректы', 'redirect', 1, '---\nno_redirect_list:\nblack_list:\nis_check_link: null\nwhite_list:\nredirect_time: 10\n', 'InstantCMS Team', 'http://www.instantcms.ru', '2.0', 1, NULL);");
    }
    if(!$core->db->getRowsCount('controllers', "name = 'commentsvk'")){
        $core->db->query("INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`) VALUES ('Комментарии Вконтакте', 'commentsvk', 1, '---\napi_id: \nredesign: null\nautoPublish: 1\nnorealtime: null\nmini: 0\nattach:\n  - graffiti\n  - photo\n  - video\n  - audio\nlimit: 50\n', 'InstantCMS Team', 'http://www.instantcms.ru', '1.0', 1, NULL)");
    }

    if(!$core->db->getRowsCount('activity_types', "name = 'vote.comment'")){
        $core->db->query("INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES (1, 'comments', 'vote.comment', 'Оценка комментария', 'оценил комментарий на странице %s');");
    }

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'messages'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_active`) VALUES ('Очистка удалённых личных сообщений', 'messages', 'clean', '1440', '1');");
    }

    if(!isFieldExists('rating_log', 'ip')){
        $core->db->query("ALTER TABLE `{#}rating_log` ADD `ip` INT(10) UNSIGNED NULL DEFAULT NULL, ADD INDEX (`ip`)");
    }

    if(!isFieldExists('content_datasets', 'description')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `description` TEXT NULL DEFAULT NULL AFTER `title`");
    }

    if(!isFieldExists('content_datasets', 'seo_keys')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `seo_keys` VARCHAR(256) NULL DEFAULT NULL");
    }

    if(!isFieldExists('content_datasets', 'seo_desc')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `seo_desc` VARCHAR(256) NULL DEFAULT NULL");
    }

    if(!isFieldExists('comments', 'is_approved')){
        $core->db->query("ALTER TABLE `{#}comments` ADD `is_approved` TINYINT(1) UNSIGNED NULL DEFAULT '1'");
    }

    if(isFieldExists('{users}', 'auth_token')){
        $core->db->query("ALTER TABLE `{users}` DROP `auth_token`");
    }

    if(!isFieldExists('{users}_messages', 'is_deleted')){
        $core->db->query("ALTER TABLE `{users}_messages` ADD `is_deleted` TINYINT(1) UNSIGNED NULL DEFAULT NULL");
    }

    if(!isFieldExists('{users}_messages', 'date_delete')){
        $core->db->query("ALTER TABLE `{users}_messages` ADD `date_delete` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата удаления' AFTER `date_pub`, ADD INDEX (`date_delete`);");
    }

    $core->db->query("ALTER TABLE `{#}comments` CHANGE `author_url` `author_url` VARCHAR( 15 ) NULL DEFAULT NULL COMMENT 'ip адрес'");
    $core->db->query("ALTER TABLE `{users}_messages` CHANGE `date_pub` `date_pub` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания'");

    $remove_table_indexes = array(
        '{users}_contacts' => array(
            'user_id', 'contact_id'
        ),
        '{users}_ignors' => array(
            'user_id', 'ignored_id'
        ),
        '{users}_messages' => array(
            'from_id', 'to_id', 'date_pub', 'is_new'
        ),
    );

    $add_table_indexes = array(
        'comments' => array(
            'author_url' => array('author_url'),
            'date_pub' => array('date_pub')
        ),
        '{users}_contacts' => array(
            'user_id' => array('user_id', 'contact_id'),
            'contact_id' => array('contact_id', 'user_id')
        ),
        '{users}_ignors' => array(
            'user_id' => array('user_id'),
            'ignored_user_id' => array('ignored_user_id', 'user_id')
        ),
        '{users}_messages' => array(
            'from_id' => array('from_id', 'to_id', 'is_deleted'),
            'to_id' => array('to_id', 'is_new', 'is_deleted'),
        ),
    );

    // удаляем ненужные индексы
    if($remove_table_indexes){
        foreach ($remove_table_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name) {
                $core->db->dropIndex($table, $index_name);
            }
        }
    }
    // добавляем нужные
    if($add_table_indexes){
        foreach ($add_table_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($table, $fields, $index_name);
            }
        }
    }

    // в ленте делаем  относительные урлы
    $root = cmsConfig::get('root');
    $root_len = strlen($root)+1;
    $core->db->query("UPDATE `{#}activity` SET `subject_url` = SUBSTRING(`subject_url`, {$root_len}) WHERE `subject_url` IS NOT NULL AND `subject_url` LIKE '{$root}%'");
    $core->db->query("UPDATE `{#}activity` SET `reply_url` = SUBSTRING(`reply_url`, {$root_len}) WHERE `reply_url` IS NOT NULL AND `reply_url` LIKE '{$root}%'");
    $core->db->query("UPDATE `{#}activity` SET `images` = REPLACE(`images`, 'url: {$root}', 'url: ') WHERE `images` IS NOT NULL");

}

// настройки контроллеров для пересохранения
function save_controller_options($controllers) {

    foreach ($controllers as $controller) {
        $controller_root_path = cmsConfig::get('root_path').'system/controllers/'.$controller.'/';
        $form_file = $controller_root_path.'backend/forms/form_options.php';
        $form_name = $controller.'options';
        cmsCore::loadControllerLanguage($controller);
        $form = cmsForm::getForm($form_file, $form_name, false);
        if ($form) {
            $options = $form->parse(new cmsRequest(cmsController::loadOptions($controller)));
            cmsCore::getModel('content')->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}
/**
 * Правильная функция isFieldExists
 * сделана для случая, если сначала выполнится инсталлер, а потом заменятся файлы
 */
function isFieldExists($table_name, $field) {
    $table_fields = getTableFields($table_name);
    return in_array($field, $table_fields, true);
}
function getTableFields($table) {
    $db = cmsDatabase::getInstance();
    $fields = array();
    $result = $db->query("SHOW COLUMNS FROM `{#}{$table}`");
    while($data = $db->fetchAssoc($result)){
        $fields[] = $data['Field'];
    }
    return $fields;
}
