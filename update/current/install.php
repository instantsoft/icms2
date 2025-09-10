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
    add_perms([
        'content' => [
            'manage_seo'
        ]
    ], 'flag');
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

    $db_engine = cmsConfig::get('db_engine');
    $db_charset = cmsConfig::get('db_charset');

    $core->db->query("DROP TABLE IF EXISTS `{#}billing_holds`;");

    $core->db->query("CREATE TABLE `{#}billing_holds` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `target` varchar(100) DEFAULT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `payload` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`target`)
) ENGINE={$db_engine} DEFAULT CHARSET={$db_charset};");

    $core->db->query("ALTER TABLE `{#}billing_systems` CHANGE `rate` `rate` DECIMAL(8,4) UNSIGNED NULL DEFAULT '1';");
    $core->db->query("ALTER TABLE `{users}` CHANGE `balance` `balance` DECIMAL(12,2) NULL DEFAULT '0';");

    $core->db->addTableField('billing_log', 'system_id', 'INT(11) UNSIGNED NULL DEFAULT NULL');
    $core->db->addTableField('billing_paid_fields', 'btn_titles', 'TEXT NULL DEFAULT NULL');
    $core->db->addTableField('billing_payouts', 'is_topup_balance', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');

    $options = cmsController::loadOptions('billing');

    $options['cur_real_symb'] = $options['cur_real_symb'] ?? '₽';
    $options['min_pack'] = $options['min_pack'] ?? 0;
    $options['pay_field_html'] = $options['pay_field_html'] ?? '<a class="btn btn-primary billing-buy-field" href="{url}">{solid%coins} {title}</a>';
    $options['btn_titles'] = $options['btn_titles'] ?? [];
    $options['btn_titles']['guest'] = $options['btn_titles']['guest'] ?? 'Покупка от {price}';
    $options['btn_titles']['user'] = $options['btn_titles']['user'] ?? 'Купить за {price}';
    $options['limit_log'] = $options['limit_log'] ?? 15;
    $options['plan_remind_days'] = $options['plan_remind_days'] ?? 1;
    $options['rtp_rate'] = $options['rtp_rate'] ?? 0.0001;
    $options['ptr_rate'] = $options['ptr_rate'] ?? 0.0001;
    $options['out_min'] = $options['out_min'] ?? 10;
    $options['limit_out'] = $options['limit_out'] ?? 15;
    $options['limit_refs'] = $options['limit_refs'] ?? 15;

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
            $core->db->addTableField($table_name, $column, 'DECIMAL(10,2) NULL DEFAULT NULL');
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

    if(!$model->db->getRowsCount('billing_systems', "name = 'payeer'", 1)){
        $model->insert('billing_systems', [
            'name'        => 'payeer',
            'title'       => 'PAYEER',
            'payment_url' => 'https://payeer.com/merchant/',
            'rate'        => '1.0000',
            'options'     => "---\nshop_id: \"\"\nsecret_key: \"\"\nsig_key: \"\"\ncurr: RUB\n"
        ]);
    }

    if(!$model->db->getRowsCount('billing_systems', "name = 'yandex'", 1)){
        $model->insert('billing_systems', [
            'name'        => 'yandex',
            'title'       => 'ЮMoney',
            'payment_url' => 'https://yoomoney.ru/quickpay/confirm.xml',
            'rate'        => '1.0000',
            'options'     => "---\nreceiver: \"\"\nsecret_key: \"\"\n"
        ]);
    }

    if(!$model->db->getRowsCount('billing_systems', "name = 'yakassa'", 1)){
        $model->insert('billing_systems', [
            'name'        => 'yakassa',
            'title'       => 'ЮKassa',
            'payment_url' => 'billing/prepare/yakassa',
            'rate'        => '1.0000',
            'options'     => "---\nshop_id: \"\"\nkey: \"\"\n"
        ]);
    }

    $model->delete('billing_systems', 'wmr', 'name');
    $model->delete('billing_systems', 'smscoin', 'name');
    $model->delete('billing_systems', 'enpay', 'name');

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
