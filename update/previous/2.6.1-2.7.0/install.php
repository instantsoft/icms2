<?php
/**
 * 2.6.1 => 2.7.0
 */
function install_package(){

	$core = cmsCore::getInstance();
    $content_model = cmsCore::getModel('content');

    if(!isFieldExists('content_datasets', 'cats_view')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `cats_view` TEXT NULL DEFAULT NULL COMMENT 'Показывать в категориях'");
    }

    if(!isFieldExists('content_datasets', 'cats_hide')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `cats_hide` TEXT NULL DEFAULT NULL COMMENT 'Не показывать в категориях'");
    }

    if(!isFieldExists('moderators', 'trash_left_time')){
        $content_model->db->query("ALTER TABLE `{#}moderators` ADD `trash_left_time` INT(5) NULL DEFAULT NULL");
    }

    $core->db->query("UPDATE `{#}controllers` SET `is_backend` =  '1' WHERE `name` = 'moderation'");

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'moderation' AND hook = 'trash'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_active`) VALUES ('Удаление просроченных записей из корзины', 'moderation', 'trash', '30', '1');");
    }

    $ctypes = $content_model->getContentTypes();

	foreach($ctypes as $ctype){

        if(!isFieldExists("{$content_model->table_prefix}{$ctype['name']}", 'is_deleted')){
            $content_model->db->query("ALTER TABLE `{#}{$content_model->table_prefix}{$ctype['name']}` ADD `is_deleted` TINYINT(1) UNSIGNED NULL DEFAULT NULL AFTER `rating`");
            $content_model->db->query("ALTER TABLE `{#}{$content_model->table_prefix}{$ctype['name']}_fields` CHANGE `name` `name` VARCHAR(40) NULL DEFAULT NULL");
        }

	}

    if(!$core->db->getRowsCount('widgets_pages', "controller IS NULL AND name = 'all'", 1)){
        $id = $content_model->insert('widgets_pages', array(
            'name'        => 'all',
            'title_const' => 'LANG_WP_ALL_PAGES'
        ));
        if($id){
            $content_model->update('widgets_pages', $id, array(
                'id' => 0
            ));
        }
    }

    $remove_table_indexes = array(
        '{users}_notices' => array(
            'user_id', 'date_pub'
        )
    );

    $add_table_indexes = array(
        '{users}_notices' => array(
            'user_id' => array('user_id', 'date_pub')
        )
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

    if(!isFieldExists('geo_cities', 'ordering')){
        $core->db->query("ALTER TABLE `{#}geo_cities` ADD `ordering` INT(11) unsigned NOT NULL DEFAULT '10000' AFTER `name`");
    }

    if(!isFieldExists('geo_regions', 'ordering')){
        $core->db->query("ALTER TABLE `{#}geo_regions` ADD `ordering` INT(11) unsigned NOT NULL DEFAULT '1000' AFTER `name`");
    }

    add_perms(array(
        'content' => array(
            'add_to_parent'
        )
    ), 'list', 'to_own,to_other,to_all');

    add_perms(array(
        'content' => array(
            'bind_to_parent'
        )
    ), 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all');

    add_perms(array(
        'content' => array(
            'bind_off_parent'
        )
    ), 'list', 'own,all');

    add_perms(array(
        'content' => array(
            'move_to_trash'
        )
    ), 'list', 'own,all');

    add_perms(array(
        'content' => array(
            'restore'
        )
    ), 'list', 'own,all');

    add_perms(array(
        'content' => array(
            'trash_left_time'
        )
    ), 'number');

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
        $form = cmsForm::getForm($form_file, $form_name, false);
        if ($form) {
            $options = $form->parse(new cmsRequest(cmsController::loadOptions($controller)));
            cmsCore::getModel('content')->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}

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
