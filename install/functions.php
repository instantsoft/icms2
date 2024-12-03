<?php

function is_ajax_request() {
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        return false;
    }
    return $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}

function render($template_name, $data = []) {
    extract($data);
    ob_start();
    include PATH . "templates/{$template_name}.php";
    return ob_get_clean();
}

function run_step($step, $is_submit = false) {
    require PATH . "steps/{$step['id']}.php";
    return step($is_submit);
}

function is_config_exists() {
    return is_readable(dirname(PATH) . DS . 'system/config/config.php');
}

function get_site_config_env() {

    $env_mapping = [
        'db_host'        => 'ICMS_MYSQL_DB_HOST',
        'db_base'        => 'ICMS_MYSQL_DB_BASE',
        'db_user'        => 'ICMS_MYSQL_DB_USER',
        'db_pass'        => 'ICMS_MYSQL_DB_PASS',
        'db_prefix'      => 'ICMS_MYSQL_DB_PREFIX',
        'db_engine'      => 'ICMS_MYSQL_DB_ENGINE',
        'db_charset'     => 'ICMS_MYSQL_DB_CHARSET',
        'clear_sql_mode' => 'ICMS_MYSQL_CLEAR_SQL_MODE',
        'db_users_table' => 'ICMS_MYSQL_DB_USERS_TABLE',
        'language'       => 'ICMS_LANGUAGE'
    ];

    $config = [];

    foreach ($env_mapping as $config_key => $env_key) {
        $config[$config_key] = getenv($env_key);
    }

    return array_filter($config);
}

function get_site_config() {

    static $cfg = null;

    if(isset($cfg)){ return $cfg; }

    $cfg_file = dirname(PATH).DS.'system/config/config.php';

    if(!is_readable($cfg_file)){
        return get_site_config_env();
    }

    return include $cfg_file;
}

function is_db_connected() {

    $cfg = get_site_config();

    if ($cfg) {

        $mysqli = @new mysqli($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_base']);

        if (!$mysqli->connect_error) {
            return true;
        }
    }

    return false;
}

function get_db_list() {

    $cfg = get_site_config();

    if ($cfg) {

        $mysqli = @new mysqli($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_base']);

        if (!$mysqli->connect_error) {

            $r = $mysqli->query('SHOW DATABASES');
            if (!$r) {
                return false;
            }

            $list = [];

            while ($data = $r->fetch_assoc()) {
                if (in_array($data['Database'], ['information_schema', 'mysql', 'performance_schema', 'phpmyadmin', 'sys'])) {
                    continue;
                }
                $list[$data['Database']] = $data['Database'];
            }

            return $list;
        }
    }

    return false;
}

function get_version($show_date = false) {

    $file = dirname(PATH) . DS . 'system/config/version.ini';

    if (!is_readable($file) || !function_exists('parse_ini_file')) {
        return '';
    }

    $version = parse_ini_file($file);

    $version_str = $version['major'] . '.' . $version['minor'] . '.' . $version['build'];

    $is_custom = $version['is_custom'] ?? 0;

    return $version_str.($is_custom ? '-custom' : '').($show_date ? ' '.$version['date'] : '');
}

function html_bool_span($value, $condition) {
    if ($condition) {
        return '<span class="positive">' . $value . '</span>';
    } else {
        return '<span class="negative">' . $value . '</span>';
    }
}

function get_langs() {

    $dir         = PATH . 'languages';
    $dir_context = opendir($dir);

    $list = [];

    while ($next = readdir($dir_context)) {

        if (in_array($next, ['.', '..'])) {
            continue;
        }
        if (strpos($next, '.') === 0) {
            continue;
        }
        if (!is_dir($dir . '/' . $next)) {
            continue;
        }

        $list[] = $next;
    }

    return $list;
}

function get_templates() {

    $dir         = dirname(PATH) . DS . 'templates';
    $dir_context = opendir($dir);

    $list = [];

    while ($next = readdir($dir_context)) {

        if (in_array($next, ['.', '..'])) {
            continue;
        }
        if (strpos($next, '.') === 0) {
            continue;
        }
        if (!is_dir($dir . '/' . $next)) {
            continue;
        }

        // не даём выбрать устаревший шаблон
        if($next !== 'default'){
            $list[$dir . '/' . $next] = $next;
        }
    }

    return $list;
}

function get_packages_sql_list() {

    $dir_path = PATH . 'languages' . DS . LANG . DS . 'sql' . DS . 'packages' . DS;

    return array_values(array_filter(scandir($dir_path, SCANDIR_SORT_ASCENDING), function ($entry) use ($dir_path) {

        return $entry !== '.' && $entry !== '..' &&
               is_dir($dir_path . $entry);
    }));
}

function get_files_list($directory, $pattern = '*.*', $is_strip_ext = false) {

    $pattern = $directory . '/' . $pattern;

    $list = [];

    $files = glob($pattern);

    if (!$files) { return $list; }

    foreach ($files as $file) {

        $file = basename($file);

        if ($is_strip_ext) {
            $file = pathinfo($file, PATHINFO_FILENAME);
        }

        $list[] = $file;
    }

    return $list;
}

function copy_folder($dir_source, $dir_target) {

    if (is_dir($dir_source)) {

        @mkdir($dir_target);
        $d = dir($dir_source);

        while (false !== ($entry = $d->read())) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            copy_folder("$dir_source/$entry", "$dir_target/$entry");
        }

        $d->close();
    } else {
        @copy($dir_source, $dir_target);
    }

}

function execute_command($command, $postfix = ' 2>&1') {
    if (!function_exists('exec')) {
        return false;
    }
    $buffer = [];
    $err    = '';
    $result = exec($command . $postfix, $buffer, $err);
    if ($err !== 127) {
        if (!isset($buffer[0])) {
            $buffer[0] = $result;
        }
        // проверяем, что команда такая есть
        $b = mb_strtolower($buffer[0]);
        if (mb_strstr($b, 'error') || mb_strstr($b, ' no ') || mb_strstr($b, 'not found') || mb_strstr($b, 'No such file or directory')) {
            return false;
        }
    } else {
        // команда не найдена
        return false;
    }

    return $buffer;
}

function get_program_path($program) {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        //$which = 'where';
        return false;
    } else {
        $which = '/usr/bin/which';
    }
    $data = execute_command($which . ' ' . $program);
    if (!$data) {
        return false;
    }
    return !empty($data[0]) ? $data[0] : false;
}

function get_post($name) {
    return (isset($_POST[$name]) && !is_array($_POST[$name])) ? trim((string)$_POST[$name]) : '';
}

function get_post_array($name) {
    return (isset($_POST[$name]) && is_array($_POST[$name])) ? $_POST[$name] : [];
}

function delete_manifest_files ($manifest) {
    if (!empty($manifest['dirs'])) {
        foreach ($manifest['dirs'] as $dir_path) {
            files_remove_directory(PATH_ICMS . $dir_path);
        }
    }
    if (!empty($manifest['files'])) {
        foreach ($manifest['files'] as $file_path) {
            if(is_file(PATH_ICMS . $file_path)){
                @unlink(PATH_ICMS . $file_path);
            }
        }
    }
}

function get_console_confirm() {
    return strtolower(trim(fgets(STDIN))) === 'y' ? true : false;
}
