<?php
/**
 * 2.14.1 => 2.14.2
 */
function install_package(){

    $core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    if(!$core->db->isFieldExists('widgets_bind', 'tpl_wrap_custom')){
        $core->db->query("ALTER TABLE `{#}widgets_bind` ADD `tpl_wrap_custom` TEXT NULL DEFAULT NULL AFTER `tpl_wrap`");
    }

    migrateCommentsIps();
    migrateRatingLogIps();
    migrateAuthTokensIps();

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
            cmsCore::getModel('content')->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}

function migrateAuthTokensIps() {

    $model = new cmsModel();

    $model->db->query("TRUNCATE `{users}_auth_tokens`");
    $model->db->query("ALTER TABLE `{users}_auth_tokens` CHANGE `ip` `ip` VARBINARY(16) NULL DEFAULT NULL");

    return true;
}

function migrateRatingLogIps() {

    $model = new cmsModel();

    $table_fields = $model->db->getTableFieldsTypes('rating_log');

    if($table_fields['ip'] == 'varbinary'){
        return false;
    }

    $logs = $model->selectOnly('ip')->select('id')->limit(false)->get('rating_log', function($item, $model){
        return $item['ip'];
    });

    $model->db->query("ALTER TABLE `{#}rating_log` CHANGE `ip` `ip` VARBINARY(16) NULL DEFAULT NULL COMMENT 'ip-адрес проголосовавшего';");

    if($logs){
        foreach ($logs as $id => $ip) {

            $model->filterEqual('id', $id)->updateFiltered('rating_log', [
                'ip' => function ($db) use($ip){
                    return '\''.$db->escape(string_iptobin($ip)).'\'';
                }
            ], true);
        }
    }

    return true;
}

function migrateCommentsIps() {

    $model = new cmsModel();

    if($model->db->isFieldExists('comments', 'author_ip')){
        return false;
    }

    $comments = $model->selectOnly('author_url')->select('id')->limit(false)->get('comments', function($item, $model){
        return $item['author_url'];
    });

    $model->db->query("ALTER TABLE `{#}comments` CHANGE `author_url` `author_ip` VARBINARY(16) NULL DEFAULT NULL COMMENT 'ip адрес'");

    if($comments){
        foreach ($comments as $id => $author_ip) {

            $model->filterEqual('id', $id)->updateFiltered('comments', [
                'author_ip' => function ($db) use($author_ip){
                    return '\''.$db->escape(string_iptobin($author_ip)).'\'';
                }
            ], true);
        }
    }

    return true;
}
