<?php
/**
 * Простая кастомизация архива InstantCMS
 * В файле /install/manifest.php указываются компоненты, которые:
 * - должны быть удалены из полной сборки
 * - необходимо добавить в сборку
 *
 * Для упаковки/распаковки в ZIP должен быть установлен zip/unzip на сервере
 *
 * Запуск: php -f rebuild.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    die('404');
}
// Пока только так. Принимаем пул-реквесты :)
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    exit('Script does not work on Windows' . PHP_EOL);
}
if (!function_exists('exec')) {
    exit('exec function is unavailable in PHP' . PHP_EOL);
}
if (!function_exists('parse_ini_file')) {
    exit('parse_ini_file function is unavailable in PHP' . PHP_EOL);
}

define('PATH', dirname(__FILE__).'/');
define('PATH_ICMS', dirname(PATH).'/');

include_once PATH . 'functions.php';

include_once PATH_ICMS . 'system/libs/files.helper.php';

$version_file = PATH_ICMS.'system/config/version.ini';
$version = parse_ini_file($version_file);
$core_version = [
    'date'    => $version['date'],
    'version' => $version['major'] . '.' . $version['minor'] . '.' . $version['build']
];

$all_langs = get_langs();

$_lang = 'en';

if (($sys_locale = exec('echo $LANG'))) {
    $user_lang = strtolower(substr($sys_locale, 0, 2));
    if (in_array($user_lang, $all_langs)) {
        $_lang = $user_lang;
    }
}

define('LANG', $_lang);

include PATH . 'languages/' . LANG . '/language.php';

echo PHP_EOL .'#### ##    ##  ######  ########    ###    ##    ## ########  ######  ##     ##  ######
 ##  ###   ## ##    ##    ##      ## ##   ###   ##    ##    ##    ## ###   ### ##    ##
 ##  ####  ## ##          ##     ##   ##  ####  ##    ##    ##       #### #### ##
 ##  ## ## ##  ######     ##    ##     ## ## ## ##    ##    ##       ## ### ##  ######
 ##  ##  ####       ##    ##    ######### ##  ####    ##    ##       ##     ##       ##
 ##  ##   ### ##    ##    ##    ##     ## ##   ###    ##    ##    ## ##     ## ##    ##
#### ##    ##  ######     ##    ##     ## ##    ##    ##     ######  ##     ##  ######  '. PHP_EOL;
echo PHP_EOL."\e[34m\e[1m#########################   ".sprintf(LANG_RB_TITLE, $core_version['version'])."   ##########################\e[0m".PHP_EOL.PHP_EOL;

$manifest = include PATH . 'manifest.php';

if (!$manifest) {

    fopen('php://stdin', 'r');

    echo "\e[33m".LANG_RB_DEL_ALL." (Y/N): ";

    $is_delete_all = get_console_confirm();

    if (!$is_delete_all) {

        // @todo сделать выбор из консоли
        exit(LANG_RB_ERROR_MANIFEST . PHP_EOL);
    }

    $manifest['removed'] = get_files_list(PATH.'manifests', '*.php', true);
}

if (!isset($manifest['create_archive'])) {

    fopen('php://stdin', 'r');

    echo "\e[33m".LANG_RB_CREATE_ZIP." (Y/N): ";

    $is_create_archive = get_console_confirm();

} else {

    echo "\e[33m";

    $is_create_archive = $manifest['create_archive'];
}

echo PHP_EOL.LANG_RB_START.PHP_EOL.PHP_EOL;

if (!empty($manifest['removed'])) {
    foreach ($manifest['removed'] as $controller_name) {

        echo sprintf(LANG_RB_DEL_COM, $controller_name).PHP_EOL;

        $controller_manifest_path = PATH . 'manifests/' . $controller_name . '.php';

        if (is_readable($controller_manifest_path)) {

            $controller_manifest = include $controller_manifest_path;

            delete_manifest_files($controller_manifest);
        }

        foreach ($all_langs as $lang) {
            files_remove_directory(PATH.'languages/'.$lang.'/sql/packages/'.$controller_name);
        }
    }
}

if (!empty($manifest['added'])) {

    $addon_ids = [];

    foreach ($manifest['added'] as $addon_id) {
        if (!is_numeric($addon_id)) {
            $pattern = '/\[addon\]([0-9]+)\[\/addon\]/sui';
            preg_match($pattern, $addon_id, $matches);
            $addon_id = $matches[1] ?? 0;
        }
        if (!$addon_id) {
            continue;
        }
        $addon_ids[] = $addon_id;
    }

    if ($addon_ids) {

        $addons = get_addons_by_id($addon_ids);

        foreach ($addons as $addon) {

            if ($addon['is_paid']) {
                continue;
            }

            echo sprintf(LANG_RB_ADD_COM, $addon['title']).PHP_EOL;

            preinstall_addon($addon);
        }
    }
}

echo PHP_EOL.LANG_RB_DEL_SER.PHP_EOL;

files_remove_directory(PATH_ICMS . '.git/');
files_remove_directory(PATH_ICMS . '.github/');
files_remove_directory(PATH_ICMS . 'update/');
@unlink(PATH_ICMS . 'LICENSE');
@unlink(PATH_ICMS . 'README.md');

echo PHP_EOL."\e[93m".LANG_RB_SET_PERM."\e[33m".PHP_EOL;

exec('find '.PATH_ICMS.' -type f -exec chmod 644 {} \;');
exec('find '.PATH_ICMS.' -type d -exec chmod 755 {} \;');
exec('chmod 777 '.PATH_ICMS.'system/config');
exec('find '.PATH_ICMS.'cache -type d -exec chmod 777 {} \;');
exec('find '.PATH_ICMS.'upload -type d -exec chmod 777 {} \;');
exec('find '.PATH_ICMS.'templates/modern/css/ -name "*.css" -exec chmod 666 {} \;');
exec('find '.PATH_ICMS.'templates/modern/controllers/ -name "*.css" -exec chmod 666 {} \;');

exec('find '.PATH_ICMS.' -name ".gitignore" -exec rm {} \; ');

file_put_contents($version_file, "\nis_custom = 1", FILE_APPEND);

if ($is_create_archive) {

    echo PHP_EOL.LANG_RB_START_ARCH.PHP_EOL;

    $archive_path = dirname(PATH_ICMS)."/instantcms_{$core_version['date']}_v{$core_version['version']}-custom.zip";

    exec('cd '.PATH_ICMS.'; /usr/bin/zip -FSr '.$archive_path.' .');

    exit(PHP_EOL."\e[32m".sprintf(LANG_RB_SUCCES_ARCH, $archive_path)."\e[0m".PHP_EOL.PHP_EOL.PHP_EOL);
}

echo PHP_EOL."\e[32m".LANG_RB_DONE.PHP_EOL;
exit(LANG_RB_DONE_HINT."\e[0m".PHP_EOL.PHP_EOL.PHP_EOL);
