<?php
/**
 * 2.18.0 => 2.18.1
 */
function install_package() {

    $core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

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
