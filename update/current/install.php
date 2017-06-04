<?php
/**
 * 2.7.2 => 2.7.3
 */
function install_package(){

	$core = cmsCore::getInstance();

    save_controller_options(array('photos'));

    $core->db->query("UPDATE `{users}_fields` SET `is_in_list` =  '1' WHERE `name` = 'nickname'");
    $core->db->query("UPDATE `{users}_fields` SET `is_in_list` =  '1' WHERE `name` = 'avatar'");

    $core->db->query("ALTER TABLE `{#}groups` ENGINE = MYISAM");

    if(!isFieldExists('groups', 'cover')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `cover` TEXT NULL DEFAULT NULL COMMENT 'Обложка группы'");
    }

    if(!isFieldExists('groups', 'slug')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `slug` VARCHAR(100) NULL DEFAULT NULL , ADD INDEX (`slug`);");
    }

    if(!isFieldExists('groups', 'content_policy')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `content_policy` VARCHAR(500) NULL DEFAULT NULL");
    }

    if(!isFieldExists('groups', 'content_groups')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `content_groups` VARCHAR(1000) NULL DEFAULT NULL");
    }

    if(!isFieldExists('groups', 'content_roles')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `content_roles` VARCHAR(1000) NULL DEFAULT NULL");
    }

    if(!isFieldExists('groups', 'roles')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `roles` VARCHAR(2000) NULL DEFAULT NULL");
    }

    if(!isFieldExists('groups', 'wall_reply_policy')){
        $core->db->query("ALTER TABLE `{#}groups` ADD `wall_reply_policy` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Политика комментирования стены' AFTER  `wall_policy`");
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
        'groups' => array(
            'owner_id', 'is_public', 'is_closed'
        ),
        'groups_members' => array(
            'date_updated', 'group_id'
        )
    );
    $add_table_indexes = array(
        'groups' => array(
            'owner_id' => array('owner_id', 'members_count')
        ),
        'groups_members' => array(
            'group_id' => array('group_id', 'date_updated')
        )
    );
    $add_table_ft_indexes = array(
        'groups' => array(
            'title' => array('title')
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
    if($add_table_ft_indexes){
        foreach ($add_table_ft_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($table, $fields, $index_name, 'FULLTEXT');
            }
        }
    }

    add_perms(array(
        'groups' => array(
            'invite_users'
        )
    ), 'flag');

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
