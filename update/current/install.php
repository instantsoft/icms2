<?php
/**
 * 2.13.1 => 2.14.0
 */
function install_package(){

    $core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('widgets_bind', 'tpl_wrap_style')){
        $core->db->query("ALTER TABLE `{#}widgets_bind` ADD `tpl_wrap_style` VARCHAR(50) NULL DEFAULT NULL AFTER `tpl_wrap`;");
    }

    if(!$core->db->isFieldExists('{users}', 'slug')){
        $core->db->query("ALTER TABLE `{users}` ADD `slug` VARCHAR(100) NULL DEFAULT NULL AFTER `nickname`, ADD INDEX (`slug`);");
    }

    if(!$core->db->isFieldExists('geo_countries', 'is_enabled')){
        $core->db->query("ALTER TABLE `{#}geo_countries` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `ordering`;");
    }

    if(!$core->db->isFieldExists('geo_regions', 'is_enabled')){
        $core->db->query("ALTER TABLE `{#}geo_regions` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `ordering`;");
    }

    if(!$core->db->isFieldExists('geo_cities', 'is_enabled')){
        $core->db->query("ALTER TABLE `{#}geo_cities` ADD `is_enabled` TINYINT(1) UNSIGNED NULL DEFAULT '1' AFTER `ordering`;");
    }

    if(!$core->db->getRowsCount('widgets', "`controller` IS NULL AND `name` = 'template'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`) VALUES (NULL, 'template', 'Элементы шаблона', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL);");
    }

    if(!$core->db->getRowsCount('widgets', "`controller` = 'content' AND `name` = 'fields'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`) VALUES ('content', 'fields', 'Поля контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL);");
    }

    if(!$core->db->getRowsCount('widgets', "`controller` = 'forms' AND `name` = 'form'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`) VALUES ('forms', 'form', 'Форма', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL);");
    }

    $admin->model->filterEqual('w.controller', 'content');
    $admin->model->filterEqual('w.name', 'list');
    $admin->model->joinInner('widgets', 'w', 'w.id = i.widget_id');
    $admin->model->selectOnly('i.id');
    $admin->model->select('i.options');
    $widgets = $admin->model->get('widgets_bind', function ($item, $model){
        $item['options'] = cmsModel::yamlToArray($item['options']);
        return $item;
    });
    if($widgets){
        foreach ($widgets as $widget) {
            if(array_key_exists('style', $widget['options'])){

                $update = [];

                if($widget['options']['style']){
                    $update['tpl_body'] = $widget['options']['style'] == 'basic' ? 'list' : 'list_'.$widget['options']['style'];
                }

                unset($widget['options']['style']);
                $update['options'] = $widget['options'];

                $admin->model->update('widgets_bind', $widget['id'], $update);
            }
        }
    }

    $core->db->importDump(dirname(__FILE__).'/install_modern_widgets.sql');

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    add_perms(array(
        'users' => array(
            'change_slug'
        )
    ), 'flag');
    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Индексы //////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    $remove_table_indexes = array();
    $add_table_uq_indexes = array();

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

    save_controller_options(array('photos', 'messages', 'tags'));

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
