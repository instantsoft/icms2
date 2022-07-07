<?php

function step($is_submit) {

    $root = $_SESSION['install']['paths']['root'];

    $path = $_SERVER['DOCUMENT_ROOT'] . $root . 'system/config';
    $file = 'config.php';

    if ($is_submit) {
        return create_config($path, $file);
    }

    return [
        'html' => render('step_config', [
            'path' => $path,
            'file' => $file,
        ])
    ];
}

function create_config($path, $file) {

    if (!is_writable($path)) {
        return [
            'error'   => true,
            'message' => LANG_CONFIG_NOT_WRITABLE
        ];
    }

    $file = $path . '/' . $file;

    $config = [
        'root'                        => $_SESSION['install']['paths']['root'],
        'host'                        => $_SESSION['install']['hosts']['root'],
        'upload_root'                 => $_SESSION['install']['paths']['upload'],
        'upload_host'                 => $_SESSION['install']['hosts']['upload'],
        'cache_root'                  => $_SESSION['install']['paths']['cache'],
        'is_site_on'                  => 1,
        'off_reason'                  => LANG_CFG_OFF_REASON,
        'sitename'                    => $_SESSION['install']['site']['sitename'],
        'hometitle'                   => $_SESSION['install']['site']['hometitle'],
        'date_format'                 => LANG_CFG_DATE_FORMAT,
        'date_format_js'              => LANG_CFG_DATE_FORMAT_JS,
        'time_zone'                   => LANG_CFG_TIME_ZONE,
        'allow_users_time_zone'       => 1,
        'template'                    => $_SESSION['install']['site']['template'],
        'template_admin'              => $_SESSION['install']['site']['template_admin'],
        'template_mobile'             => '',
        'template_tablet'             => '',
        'db_host'                     => $_SESSION['install']['db']['host'],
        'db_base'                     => $_SESSION['install']['db']['base'],
        'db_user'                     => $_SESSION['install']['db']['user'],
        'db_pass'                     => $_SESSION['install']['db']['pass'],
        'db_prefix'                   => $_SESSION['install']['db']['prefix'],
        'db_engine'                   => $_SESSION['install']['db']['engine'],
        'db_charset'                  => $_SESSION['install']['db']['db_charset'],
        'clear_sql_mode'              => $_SESSION['install']['db']['clear_sql_mode'],
        'innodb_full_text'            => $_SESSION['install']['db']['innodb_full_text'],
        'db_users_table'              => "{$_SESSION['install']['db']['users_table']}",
        'language'                    => LANG,
        'metakeys'                    => $_SESSION['install']['site']['metakeys'],
        'metadesc'                    => $_SESSION['install']['site']['metadesc'],
        'ct_autoload'                 => 'frontpage',
        'ct_default'                  => 'content',
        'frontpage'                   => 'none',
        'debug'                       => 0,
        'emulate_lag'                 => '',
        'cache_enabled'               => 0,
        'cache_method'                => 'files',
        'cache_ttl'                   => 300,
        'cache_host'                  => 'localhost',
        'cache_port'                  => 11211,
        'min_html'                    => 0,
        'merge_css'                   => 0,
        'merge_js'                    => 0,
        'mail_transport'              => 'mail',
        'mail_from'                   => 'noreply@example.com',
        'mail_from_name'              => '',
        'mail_smtp_server'            => 'smtp.example.com',
        'mail_smtp_port'              => 25,
        'mail_smtp_auth'              => 1,
        'mail_smtp_user'              => 'user@example.com',
        'mail_smtp_pass'              => '',
        'is_check_updates'            => $_SESSION['install']['site']['is_check_updates'],
        'detect_ip_key'               => 'REMOTE_ADDR',
        'allow_ips'                   => '',
        'default_editor'              => 3,
        'show_breadcrumbs'            => 1,
        'check_spoofing_type'         => 0,
        'production_time'             => time(),
        'native_yaml'                 => function_exists('yaml_emit') ? 0 : 0, // отключим пока что для всех, не везде совместимо работает
        'session_save_handler'        => 'files',
        'session_name'                => strtoupper(uniqid('icms')),
        'session_save_path'           => $_SESSION['install']['paths']['session_save_path'],
        'session_maxlifetime'         => ini_get('session.gc_maxlifetime') / 60,
        'controllers_without_widgets' => ['admin'],
        'ctype_default'               => []
    ];

    write_config($file, $config);

    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }

    clearstatcache();

    return [
        'error' => false
    ];
}

function write_config($file, $config) {

    $dump = "<?php\n" .
            "return array(\n\n";

    foreach ($config as $key => $value) {

        $value = var_export($value, true);

        $tabs = 10 - ceil((mb_strlen($key) + 3) / 4);

        $dump .= "\t'{$key}'";
        $dump .= str_repeat("\t", $tabs);
        $dump .= "=> $value,\n";
    }

    $dump .= "\n);\n";

    return @file_put_contents($file, $dump);
}
