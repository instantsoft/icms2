<?php
/**
 * 2.7.2 => 2.8.0
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

    if(!isFieldExists('content_relations', 'target_controller')){
        $core->db->query("ALTER TABLE  `{#}content_relations` ADD `target_controller` VARCHAR(32) NOT NULL DEFAULT 'content' AFTER `title`");
    }

    if(!isFieldExists('content_relations', 'ordering')){
        $core->db->query("ALTER TABLE `{#}content_relations` ADD `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0'");
    }

    if(!isFieldExists('content_relations_bind', 'target_controller')){
        $core->db->query("ALTER TABLE `{#}content_relations_bind` ADD `target_controller` VARCHAR(32) NOT NULL DEFAULT 'content'");
    }

    if(!isFieldExists('uploaded_files', 'type')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` ADD `type` ENUM( 'file', 'image', 'audio', 'video') NOT NULL DEFAULT 'file'");
    }

    if(!isFieldExists('uploaded_files', 'target_controller')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` ADD `target_controller` VARCHAR(32) NULL DEFAULT NULL");
    }

    if(!isFieldExists('uploaded_files', 'target_subject')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` ADD `target_subject` VARCHAR(32) NULL DEFAULT NULL");
    }

    if(!isFieldExists('uploaded_files', 'target_id')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` ADD `target_id` INT(11) UNSIGNED NULL DEFAULT NULL");
    }

    if(!isFieldExists('uploaded_files', 'user_id')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` ADD `user_id` INT(11) UNSIGNED NULL DEFAULT NULL");
    }

    if(!isFieldExists('uploaded_files', 'size')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` ADD `size` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `name`");
    }

    if(!isFieldExists('uploaded_files', 'date_add')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` ADD `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }

    if(isFieldExists('uploaded_files', 'url_key')){
        $core->db->query("ALTER TABLE `{#}uploaded_files` DROP `url_key`");
    }

    if(!isFieldExists('content_datasets', 'target_controller')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `target_controller` VARCHAR(32) NULL DEFAULT NULL");
    }

    $groups_options = cmsController::loadOptions('groups');
    if(!$core->db->getRowsCount('content_datasets', "name = 'rating' AND target_controller = 'groups'", 1)){
        $core->db->insert('content_datasets', array(
            'name'              => 'rating',
            'title'             => 'Лучшие группы',
            'ordering'          => 3,
            'is_visible'        => !empty($groups_options['is_ds_rating']),
            'sorting'           => array(
                array(
                    'by' => 'rating',
                    'to' => 'desc'
                )
            ),
            'index'             => 'rating',
            'target_controller' => 'groups'
        ));
    }
    if(!$core->db->getRowsCount('content_datasets', "name = 'all' AND target_controller = 'groups'", 1)){
        $core->db->insert('content_datasets', array(
            'name'              => 'all',
            'title'             => 'Новые группы',
            'ordering'          => 2,
            'is_visible'        => 1,
            'sorting'           => array(
                array(
                    'by' => 'date_pub',
                    'to' => 'desc'
                )
            ),
            'index'             => 'date_pub',
            'target_controller' => 'groups'
        ));
    }
    if(!$core->db->getRowsCount('content_datasets', "name = 'popular' AND target_controller = 'groups'", 1)){
        $core->db->insert('content_datasets', array(
            'name'              => 'popular',
            'title'             => 'Популярные',
            'ordering'          => 1,
            'is_visible'        => !empty($groups_options['is_ds_popular']),
            'sorting'           => array(
                array(
                    'by' => 'members_count',
                    'to' => 'desc'
                )
            ),
            'index'             => 'members_count',
            'target_controller' => 'groups'
        ));
    }

    if(!$core->db->getRowsCount('rss_feeds', "ctype_name = 'comments'", 1)){
        $core->db->insert('rss_feeds', array(
            'ctype_name' => 'comments',
            'title' => 'Комментарии',
            'mapping' => array(
                'title' => 'target_title',
                'description' => 'content_html',
                'pubDate' => 'date_pub'
            ),
            'is_enabled' => 1
        ));
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
        ),
        'content_relations' => array(
            'child_ctype_id', 'ctype_id'
        ),
        'content_relations_bind' => array(
            'parent_item_id', 'child_item_id'
        ),
        'uploaded_files' => array(
            'counter'
        ),
        'content_datasets' => array(
            'ordering','is_visible'
        )
    );
    $add_table_indexes = array(
        'groups' => array(
            'owner_id' => array('owner_id', 'members_count')
        ),
        'groups_members' => array(
            'group_id' => array('group_id', 'date_updated')
        ),
        'content_relations' => array(
            'child_ctype_id' => array('child_ctype_id', 'target_controller', 'ordering'),
            'ctype_id' => array('ctype_id', 'ordering')
        ),
        'content_relations_bind' => array(
            'parent_item_id' => array('parent_item_id', 'target_controller')
        ),
        'content_relations_bind' => array(
            'child_item_id' => array('child_item_id', 'target_controller')
        ),
        'uploaded_files' => array(
            'user_id' => array('user_id'),
            'target_controller' => array('target_controller', 'target_subject', 'target_id'),
        ),
        'content_datasets' => array(
            'target_controller' => array('target_controller', 'ordering'),
        )
    );
    $add_table_ft_indexes = array(
        'groups' => array(
            'title' => array('title')
        )
    );
    $add_table_uq_indexes = array(
        'uploaded_files' => array(
            'path' => array('path')
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
    if($add_table_uq_indexes){
        foreach ($add_table_uq_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($table, $fields, $index_name, 'UNIQUE');
            }
        }
    }

    add_perms(array(
        'groups' => array(
            'invite_users'
        )
    ), 'flag');

    add_perms(array(
        'groups' => array(
            'bind_to_parent'
        ),
        'users' => array(
            'bind_to_parent'
        )
    ), 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all');

    add_perms(array(
        'groups' => array(
            'bind_off_parent'
        ),
        'users' => array(
            'bind_off_parent'
        )
    ), 'list', 'own,all');

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
