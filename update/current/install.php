<?php
/**
 * 2.7.0 => 2.7.1
 */
function install_package(){

	$core = cmsCore::getInstance();

    if(!isFieldExists('content_datasets', 'seo_title')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `seo_title` VARCHAR(256) NULL DEFAULT NULL AFTER `seo_desc`");
    }

    $core->db->query("ALTER TABLE `{#}content_types` CHANGE `seo_keys` `seo_keys` VARCHAR(256) NULL DEFAULT NULL COMMENT 'Ключевые слова'");
    $core->db->query("ALTER TABLE `{#}content_types` CHANGE `seo_desc` `seo_desc` VARCHAR(256) NULL DEFAULT NULL COMMENT 'Описание'");
    $core->db->query("ALTER TABLE `{#}content_types` CHANGE `seo_title` `seo_title` VARCHAR(256) NULL DEFAULT NULL");

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
