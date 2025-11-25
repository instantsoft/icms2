<?php

define('ADDONS_API_KEY', '8e13cb202f8bdc27dc765e0448e50d11');
define('ADDONS_API_POINT', 'https://api.instantcms.ru/{lang}api/method/');

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

function add_addons_step_if_needed($steps) {

    $addons = get_dirs_list(PATH . 'externals', true);

    if (!$addons || !function_exists('parse_ini_file')) {
        return $steps;
    }

    $new_steps = [];

    foreach ($steps as $step) {
        $new_steps[] = $step;
        if ($step['id'] === 'config') {
            $new_steps[] = ['id' => 'addons', 'title' => LANG_STEP_ADDONS];
        }
    }

    return $new_steps;
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
    return get_dirs_list(PATH . 'languages');
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

    return get_dirs_list($dir_path, true);
}

function get_dirs_list($dir, $asc_sort = false) {

    if (!is_dir($dir)) {
        return [];
    }

    $sorting_order = $asc_sort ? SCANDIR_SORT_ASCENDING : SCANDIR_SORT_NONE;

    return array_values(array_filter(scandir($dir, $sorting_order), function ($entry) use ($dir) {

        return $entry !== '.' && $entry !== '..' &&
               is_dir($dir . '/' . $entry);
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

function get_api_method($name, $params = []) {

    if (!function_exists('curl_init')) {
        return null;
    }

    $curl = curl_init();

    $lang = LANG;
    if ($lang === 'ru') {
        $lang = '';
    } else {
        $lang = 'en/';
    }

    curl_setopt($curl, CURLOPT_URL, str_replace('{lang}', $lang, ADDONS_API_POINT) . $name . '?api_key=' . ADDONS_API_KEY . '&' . http_build_query($params, '', '&'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_HTTPGET, true);

    $_data = curl_exec($curl);
    if (!$_data) {
        return false;
    }

    $data = json_decode($_data, true);

    if ($data === false) {
        return null;
    }

    return $data;
}

function get_addons_by_id($id) {

    if (is_array($id)) {
        $id = implode(',', $id);
    }

    $items = get_api_method('content.get.addons', ['ids' => $id]);

    return $items['response']['items'] ?? [];
}

function preinstall_addon ($addon) {

    $latest_version = reset($addon['versions']);

    if (!$latest_version['download_url']) {
        return false;
    }

    $version_file = tempnam(sys_get_temp_dir(), 'icms_');

    file_save_from_url($latest_version['download_url'], $version_file);

    $addon_name = preg_replace('/[^a-z]/u', '', $addon['slug']);

    $ext_path = PATH.'externals/'.$addon_name.'/';

    mkdir($ext_path);

    exec('unzip '.$version_file.' -d '.$ext_path);

    @unlink($version_file);

    // Копируем файлы
    files_copy_directory($ext_path.'package', rtrim(PATH_ICMS, '/'));

    // Удаляем директорию package
    files_remove_directory($ext_path.'package');

    // Ставим неймспейс тут, т.к. на этапе установки
    // Права доступа могут не дать это сделать
    // Немного дублирования кода из cmsInstaller
    $install_php_path = $ext_path.'install.php';
    if (file_exists($install_php_path)) {

        $install_php_text = file_get_contents($install_php_path);

        $namespace_str = 'namespace installer\install\externals\\'.$addon_name.';';

        $pos = mb_strpos($install_php_text, '<?php');

        $modified = mb_substr($install_php_text, $pos, 5).PHP_EOL.$namespace_str.mb_substr($install_php_text, 5);

        file_put_contents($install_php_path, $modified);
    }

    return true;
}
