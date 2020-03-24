<?php
/**
 * 2.8.0 => 2.8.1
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!isFieldExists('widgets_bind', 'template_layouts')){
        $core->db->query("ALTER TABLE `{#}widgets_bind` ADD `template_layouts` VARCHAR(500) NULL DEFAULT NULL AFTER `template`");
    }

    if(!isFieldExists('widgets', 'is_external')){

        $core->db->query("ALTER TABLE `{#}widgets` ADD `is_external` TINYINT(1) NULL DEFAULT '1' AFTER `version`");

        $native_widgets = array(
            array(null, 'text'), array(null, 'menu'), array(null, 'auth'),
            array(null, 'html'), array('users', 'list'), array('users', 'online'),
            array('users', 'avatar'), array('tags', 'cloud'), array('search', 'search'),
            array('photos', 'list'), array('content', 'filter'), array('content', 'slider'),
            array('content', 'categories'), array('content', 'list'), array('comments', 'list'),
            array('activity', 'list')
        );

        foreach ($native_widgets as $widget) {
            $admin->model->filterEqual('controller', $widget[0])->filterEqual('name', $widget[1])->updateFiltered('widgets', array(
                'is_external' => null
            ));
        }

    }

    if(!isFieldExists('widgets', 'files')){
        $core->db->query("ALTER TABLE `{#}widgets` ADD `files` VARCHAR(4000) NULL DEFAULT NULL AFTER `is_external`");
    }

    if(!isFieldExists('controllers', 'files')){
        $core->db->query("ALTER TABLE `{#}controllers` ADD `files` VARCHAR(4000) NULL DEFAULT NULL AFTER `is_external`");
    }

    if(!isFieldExists('controllers', 'addon_id')){

        $core->db->query("ALTER TABLE `{#}controllers` ADD `addon_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `files`");

        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '544' WHERE `name` = 'commentsvk'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '279' WHERE `name` = 'video'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '124' WHERE `name` = 'opengraph'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '325' WHERE `name` = 'ping'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '329' WHERE `name` = 'hidetext'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '364' WHERE `name` = 'loadaverage'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '533' WHERE `name` = 'stopblock'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '6' WHERE `name` = 'places'");
        $core->db->query("UPDATE `{#}controllers` SET `addon_id` = '8' WHERE `name` = 'billing'");

    }

    if(!isFieldExists('widgets', 'addon_id')){
        $core->db->query("ALTER TABLE `{#}widgets` ADD `addon_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `files`");
    }

    add_perms(array(
        'groups' => array(
            'content_access'
        )
    ), 'flag');

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
/**
 * готовимся к отказу от YAML (слишком большие накладные расходы при преобразовании) и массивы будем хранить в json
function convert_yaml_to_json($data) {

    $data = array(
        'activity' => array(
            'images'
        ),
        'content_datasets' => array(
            'filters', 'sorting', 'groups_view', 'groups_hide', 'cats_view', 'cats_hide'
        ),
        'content_relations' => array(
            'options'
        ),
        'content_types' => array(
            'options', 'labels'
        ),
        'controllers' => array(
            'options'
        ),
        'menu_items' => array(
            'options', 'groups_view', 'groups_hide'
        ),
        'widgets_bind' => array(
            'template_layouts', 'groups_view', 'groups_hide', 'options', 'device_types'
        ),
        'con_albums' => array(
            'cover_image', 'allow_groups_roles'
        ),
        'con_albums_fields' => array(
            'options', 'groups_read', 'groups_edit', 'filter_view'
        ),
        'con_articles' => array(
            'file'
        ),
        'con_articles_fields' => array(
            'options', 'groups_read', 'groups_edit', 'filter_view'
        ),
        'con_board' => array(
            'photo', 'photos'
        ),
        'con_board_fields' => array(
            'options', 'groups_read', 'groups_edit', 'filter_view'
        ),
        'con_news' => array(
            'photo'
        ),
        'con_news_fields' => array(
            'options', 'groups_read', 'groups_edit', 'filter_view'
        ),
        'con_posts' => array(
            'picture', 'allow_groups_roles'
        ),
        'con_posts_fields' => array(
            'options', 'groups_read', 'groups_edit', 'filter_view'
        ),
        'users' => array(
            'theme', 'notify_options', 'privacy_options', 'groups', 'avatar'
        ),
        'users_fields' => array(
            'options', 'groups_read', 'groups_edit', 'filter_view'
        ),
    );

    $db = cmsDatabase::getInstance();

    foreach ($data as $table => $fields) {

        $fields_string = '`'.implode('`, `', $fields).'`';

        $result = $db->query("SELECT `id`, {$fields_string} FROM `{#}{$table}`");

        if($db->mysqli->errno){ continue; }

        while($data = $db->fetchAssoc($result)){

            foreach ($fields as $field) {
                $data[$field] = json_encode(cmsModel::yamlToArray($data[$field]));
            }

            $db->update($table, "id = '{$data['id']}'", $data, true);

        }

    }

}
**/
