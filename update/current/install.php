<?php
/**
 * 2.18.2 => 2.18.3
 */
function install_package() {

    $core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('menu_items', 'hint', false)){
        $core->db->query("ALTER TABLE `{#}menu_items` ADD `hint` VARCHAR(64) NULL DEFAULT NULL COMMENT 'Краткое описание пункта меню' AFTER `title`;");
    }

    if(!$core->db->isFieldExists('{users}', 'cover', false)){
        $core->db->query("ALTER TABLE `{users}` ADD `cover` TEXT NULL DEFAULT NULL");
    }

    if(!$core->db->getRowsCount('{users}_fields', "`name` = 'cover'")){
        $core->db->query("INSERT INTO `{users}_fields` (`name`, `title`, `hint`, `ordering`, `is_enabled`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_add`, `groups_edit`, `filter_view`) VALUES ('cover', 'Обложка профиля', NULL, 3, 1, 'Анкета', 'image', NULL, 1, NULL, NULL, 1, 1, 1, NULL, '---\nsize_teaser: small\nsize_full: user_cover\nsize_modal: \"\"\nsizes:\n  - small\n  - user_cover\nallow_import_link: null\nallow_image_cropper: 1\nimage_cropper_rounded: null\nimage_cropper_ratio: 6\ndefault_image: null\nshow_to_item_link: null\nimg_attr: \"\"\nimg_attr_item: \"\"\nvisible_depend: null\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_regexp: null\nrules_regexp_str: \"\"\nrules_regexp_error: \"\"\nis_unique: null\nauthor_access: null\n', '---\n', '---\n', '---\n', '---\n')");
    }

    if(!$core->db->getRowsCount('widgets', "`controller` = 'content' AND `name` = 'datasets'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`, `addon_id`, `image_hint_path`) VALUES ('content', 'datasets', 'Наборы', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL, NULL);");
    }

    if(!$core->db->getRowsCount('widgets', "`controller` = 'content' AND `name` = 'category_info'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`, `addon_id`, `image_hint_path`) VALUES ('content', 'category_info', 'Информация о категории', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL, NULL);");
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Индексы //////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    $remove_table_indexes = [];
    $add_table_indexes = [];

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

    //save_controller_options(['billing']);

    //compile_scss_if_necessary();

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

    $model = new cmsModel();

    foreach ($controllers as $controller => $new_options) {
        if (is_numeric($controller)) {
            $controller = $new_options;
            $new_options = [];
        }
        $controller_root_path = cmsConfig::get('root_path').'system/controllers/'.$controller.'/';
        $form_file = $controller_root_path.'backend/forms/form_options.php';
        $form_name = $controller.'options';
        cmsCore::loadControllerLanguage($controller);
        cmsCore::includeFile('system/controllers/'.$controller.'/model.php');
        try {
            $form = cmsForm::getForm($form_file, $form_name, false);
            if ($form) {
                $options = $form->parse(new cmsRequest(cmsController::loadOptions($controller)));
                $model->filterEqual('name', $controller)->updateFiltered('controllers', array(
                    'options' => array_merge($options, $new_options)
                ));
            }
        } catch (Exception $exc) {
            cmsUser::addSessionMessage('Настройки компонента '.$controller.' сохранились с ошибкой. Пересохраните их самостоятельно в админке.', 'error');
        }
    }

}

function compile_scss_if_necessary() {

    $template_name = cmsConfig::get('template');

    $template = new cmsTemplate($template_name);

    $options = $template->getOptions();

    $manifest = $template->getManifest();

    if($manifest !== null && !empty($manifest['properties']['style_middleware'])){

        $renderer = cmsCore::getController('renderer', new cmsRequest([
            'middleware' => $manifest['properties']['style_middleware']
        ]), cmsRequest::CTX_INTERNAL);

        $renderer->cms_template = $template;

        $renderer->render($template_name, $options);
    }

}
