<?php
/**
 * 2.6.1 => 2.7.0
 */
function install_package(){

	$core = cmsCore::getInstance();
    $content_model = cmsCore::getModel('content');

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
