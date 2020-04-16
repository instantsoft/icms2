<?php
/**
 * 2.11.1 => 2.12.0
 */
function install_package(){

	$core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    $content_model = cmsCore::getModel('content');

    if($core->db->isFieldExists('widgets_bind', 'template')){

        $bnds = $content_model->limit(false)->get('widgets_bind');

        if($bnds){
            foreach ($bnds as $bnd) {

                $content_model->insert('widgets_bind_pages', array(
                    'bind_id'    => $bnd['id'],
                    'template'   => $bnd['template'],
                    'is_enabled' => $bnd['is_enabled'],
                    'page_id'    => $bnd['page_id'],
                    'position'   => $bnd['position'],
                    'ordering'   => $bnd['ordering']
                ));

            }
        }

        $core->db->query("ALTER TABLE `{#}widgets_bind` DROP `template`;");
        $core->db->query("ALTER TABLE `{#}widgets_bind` DROP `is_enabled`;");
        $core->db->query("ALTER TABLE `{#}widgets_bind` DROP `page_id`;");
        $core->db->query("ALTER TABLE `{#}widgets_bind` DROP `position`;");
        $core->db->query("ALTER TABLE `{#}widgets_bind` DROP `ordering`;");

    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////// Новые правила доступа ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////


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
            if($controller == 'auth'){
                $options['is_site_only_auth_users'] = cmsConfig::get('is_site_only_auth_users');
                $options['guests_allow_controllers'] = array('auth', 'geo');
            }
            cmsCore::getModel('content')->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}
