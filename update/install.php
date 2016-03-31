<?php

function install_package(){

	$core = cmsCore::getInstance();
    $content_model = cmsCore::getModel('content');

    $remove_table_indexes = array(
        'tags' => array(
            'tag'
        ),
        'widgets_bind' => array(
            'is_enabled', 'ordering', 'page_id'
        ),
    );

    $add_table_indexes = array(
        'tags_bind' => array(
            'target_controller' => array('target_controller', 'target_subject', 'tag_id')
        ),
        'widgets_bind' => array(
            'page_id' => array('page_id', 'position', 'ordering')
        ),
    );
    $add_unique_table_indexes = array(
        'tags' => array(
            'tag'      => array('tag'),
            'frequency' => array('frequency', 'tag'),
        )
    );

    // удаляем ненужные индексы
    foreach ($remove_table_indexes as $table=>$ri) {
        foreach ($ri as $index_name) {
            $core->db->dropIndex($table, $index_name);
        }
    }
    // добавляем нужные
    foreach ($add_table_indexes as $table=>$indexes) {
        foreach ($indexes as $index_name => $fields) {
            $core->db->addIndex($table, $fields, $index_name);
        }
    }
    // добавляем нужные уникальные
    foreach ($add_unique_table_indexes as $table=>$indexes) {
        foreach ($indexes as $index_name => $fields) {
            $core->db->addIndex($table, $fields, $index_name, 'UNIQUE');
        }
    }

    $ctypes = $content_model->getContentTypes();

	foreach($ctypes as $ctype){

        if(!$core->db->isFieldExists("{$content_model->table_prefix}{$ctype['name']}_cats", 'allow_add')){
            $core->db->query("ALTER TABLE `{#}{$content_model->table_prefix}{$ctype['name']}_cats` ADD `allow_add` TEXT NULL DEFAULT NULL");
        }

	}

    if(!$core->db->isFieldExists('{users}_tabs', 'groups_view')){
        $core->db->query("ALTER TABLE `{users}_tabs` ADD `groups_view` TEXT NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('{users}_tabs', 'groups_hide')){
        $core->db->query("ALTER TABLE `{users}_tabs` ADD `groups_hide` TEXT NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('{users}_tabs', 'show_only_owner')){
        $core->db->query("ALTER TABLE `{users}_tabs` ADD `show_only_owner` TINYINT(1) UNSIGNED NULL DEFAULT NULL");
    }

    if(!$core->db->isFieldExists('widgets_bind', 'template')){
        $template_name = cmsConfig::get('template');
        $core->db->query("ALTER TABLE  `{#}widgets_bind` ADD `template` VARCHAR(30) NULL DEFAULT NULL COMMENT 'Привязка к шаблону' AFTER `id`");
        $core->db->query("UPDATE `{#}widgets_bind` SET `template` = '{$template_name}', `is_enabled` = 1");
    }

    if(!$core->db->isFieldExists('{users}', 'city_cache')){
        $core->db->query("ALTER TABLE `{users}` ADD `city_cache` varchar(128) NULL DEFAULT NULL");
    }

    $users = $content_model->get('{users}');

	foreach($users as $user){

        if($user['city']){

            $city_name = $content_model->getField('geo_cities', $user['city'], 'name');
            if($city_name){
                $content_model->update('{users}', $user['id'], array(
                    'city_cache' => $city_name
                ), true);
            }

        }

	}

    $core->db->update('{users}_fields', '1=1', array(
        'is_in_item' => 1
    ), true);

    $core->db->update('{users}_fields', "type = 'city'", array(
        'is_fixed'      => null,
        'is_fixed_type' => null,
        'is_system'     => null
    ), true);

    // настройки контроллеров для пересохранения
    save_controller_options(array('auth'));

}

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