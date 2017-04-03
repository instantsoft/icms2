<?php
/**
 * 2.7.1 => 2.7.2
 */
function install_package(){

	$core = cmsCore::getInstance();

    $core->db->query("UPDATE `{#}controllers` SET `is_external` =  '1' WHERE `name` = 'commentsvk'");

    if(!isFieldExists('widgets_pages', 'groups')){
        $core->db->query("ALTER TABLE `{#}widgets_pages` ADD `groups` TEXT NULL DEFAULT NULL");
    }

    if(!isFieldExists('widgets_pages', 'countries')){
        $core->db->query("ALTER TABLE `{#}widgets_pages` ADD `countries` TEXT NULL DEFAULT NULL");
    }

    if(!isFieldExists('widgets_bind', 'device_types')){
        $core->db->query("ALTER TABLE `{#}widgets_bind` ADD `device_types` VARCHAR(50) NULL DEFAULT NULL");
    }

    if(!isFieldExists('content_datasets', 'max_count')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `max_count` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'");
    }

    if(!isFieldExists('{users}', 'is_deleted')){
        $core->db->query("ALTER TABLE `{users}` ADD `is_deleted` TINYINT(1) UNSIGNED NULL DEFAULT NULL AFTER `ip`");
    }

    $admin = cmsCore::getController('admin');

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

    $remove_table_indexes = array(
        'content_folders' => array(
            'user_id'
        )
    );
    $add_table_indexes = array(
        'content_folders' => array(
            'user_id' => array('user_id', 'ctype_id', 'title')
        )
    );
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

    add_perms(array(
        'users' => array(
            'delete'
        )
    ), 'list', 'my,anyuser');

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
