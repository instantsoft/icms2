<?php
/**
 * 2.17.3 => 2.18.0
 */
function install_package() {

    $core = cmsCore::getInstance();
    $admin = cmsCore::getController('admin');

    $core->db->query("ALTER TABLE `{#}jobs` CHANGE `payload` `payload` MEDIUMTEXT NULL DEFAULT NULL;");

    if (!update_billing()) {
        install_billing();
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

    //compile_scss_if_necessary();

    return true;
}

function install_billing() {

    $model = cmsCore::getModel('content');

    if ($model->filterEqual('version', '2.1.0')->getItemByField('controllers', 'name', 'billing')) {
        return false;
    }

    $file = cmsConfig::get('upload_path') . 'installer/' . 'install_billing.sql';

    $model->db->importDump($file);

    $ctypes = $model->getContentTypes() ?: [];

    foreach ($ctypes as $ctype) {

        $name  = $model->db->escape($ctype['name'] . '_add');
        $title = $model->db->escape($ctype['title'] . ': добавление');

        $sql = "INSERT INTO `{#}billing_actions` (`controller`, `name`, `title`) VALUES
                ('content',  '{$name}',  '{$title}')";

        $model->db->query($sql);
    }

    if ($model->db->isFieldExists('{users}', 'balance', false)) {
        $model->db->query("ALTER TABLE `{users}` CHANGE `balance` `balance` DECIMAL(12,2) NULL DEFAULT '0.00';");
    } else {
        $model->db->query("ALTER TABLE `{users}` ADD `balance` decimal(12,2) DEFAULT 0.00 AFTER  `email`");
    }

    $model->db->addTableField('{users}', 'plan_id', 'int(11) UNSIGNED DEFAULT NULL AFTER  `balance`');

    $model->db->addIndex('{users}', 'balance');
    $model->db->addIndex('{users}', 'plan_id');
}

function update_billing() {

    $core = cmsCore::getInstance();
    $model = new cmsModel();

    $billing = $model->getItemByField('controllers', 'name', 'billing');

    if (!$billing) {
        return false;
    }

    if ($billing['version'] === '2.1.0') {
        return true;
    }

    // На всякий случай проверяем наличие таблиц
    foreach (['billing_systems', 'billing_log', 'billing_paid_fields'] as $table) {
        if (!$core->db->isTableExists($table)) {
            return false;
        }
    }

    $core->db->query("ALTER TABLE `{#}billing_systems` CHANGE `rate` `rate` DECIMAL(8,4) UNSIGNED NULL DEFAULT '1';");
    $core->db->query("ALTER TABLE `{users}` CHANGE `balance` `balance` DECIMAL(12,2) NULL DEFAULT '0';");

    $core->db->addTableField('billing_log', 'system_id', 'INT(11) UNSIGNED NULL DEFAULT NULL');
    $core->db->addTableField('billing_paid_fields', 'btn_titles', 'TEXT NULL DEFAULT NULL');

    $options = cmsController::loadOptions('billing');

    $formatted_prices = [];
    foreach ($options['prices']['amount']??[] as $key => $value) {
        $formatted_prices[] = [
            'amount' => $value,
            'price'  => $options['prices']['price'][$key] ?? 0
        ];
    }

    $ref_levels = [];
    foreach ($options['ref_levels']??[] as $key => $value) {
        if (!is_array($value)) {
            $ref_levels[] = [
                'percent' => $value
            ];
        }
    }

    if ($ref_levels) {
        $options['ref_levels'] = $ref_levels;
    }

    if ($formatted_prices) {
        $options['prices'] = $formatted_prices;
    }

    cmsController::saveOptions('billing', $options);

    $replace_floats = [
        'billing_log' => [
            'amount', 'summ'
        ],
        'billing_outs' => [
            'amount', 'summ'
        ],
        'billing_payouts' => [
            'amount'
        ],
        'billing_plans' => [
            'max_out'
        ],
        'billing_transfers' => [
            'amount'
        ]
    ];

    foreach ($replace_floats as $table_name => $columns) {
        if (!$core->db->isTableExists($table_name)) {
            continue;
        }
        foreach ($columns as $column) {
            $core->db->query("ALTER TABLE `{#}{$table_name}` CHANGE `{$column}` `{$column}` DECIMAL(10,2) NULL DEFAULT NULL;");
        }
    }

    $logs = $model->filterNotNull('url')->limit(false)->selectOnly('id')->select('url')->get('billing_log') ?: [];

    foreach ($logs as $log) {

        $lang_href = cmsCore::getLanguageHrefPrefix();

        $replace = cmsConfig::get('root') .($lang_href ? $lang_href.'/' : '');

        $log['url'] = preg_replace('#^('.preg_quote($replace).')(.*)$#u', '$2', $log['url']);

        $model->update('billing_log', $log['id'], [
            'url' => $log['url']
        ]);
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
