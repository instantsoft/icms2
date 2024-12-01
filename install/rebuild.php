<?php
/**
 * Простая кастомизация архива InstantCMS
 * В файле /install/manifest.php указываются компоненты, которые:
 * - должны быть удалены из полной сборки
 * - необходимо добавить в сборку
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

$version_file = PATH_ICMS.'system/config/version.ini';
$version = parse_ini_file($version_file);
$core_version = [
    'date'    => $version['date'],
    'version' => $version['major'] . '.' . $version['minor'] . '.' . $version['build']
];

$all_langs = get_langs();

$manifest = include PATH . 'manifest.php';

if (!array_filter($manifest)) {
    exit('Complete the manifest.php file' . PHP_EOL);
}

echo PHP_EOL."\e[34m\e[1m###### InstantCMS {$core_version['version']} customization ######\e[0m".PHP_EOL.PHP_EOL;

fopen('php://stdin', 'r');

echo "\e[33mCreate a zip archive? (Y/N)\n";

$is_create_archive = strtolower(trim(fgets(STDIN))) === 'y' ? true : false;

echo PHP_EOL.'Customizing InstantCMS...'.PHP_EOL.PHP_EOL;

if (!empty($manifest['removed'])) {
    foreach ($manifest['removed'] as $controller_name) {

        echo 'Deleting the '.$controller_name.' component'.PHP_EOL;

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

echo PHP_EOL.'Deleting service files...'.PHP_EOL;

files_remove_directory(PATH_ICMS . '.git/');
files_remove_directory(PATH_ICMS . '.github/');
files_remove_directory(PATH_ICMS . 'update/');
@unlink(PATH_ICMS . 'LICENSE');
@unlink(PATH_ICMS . 'README.md');

echo PHP_EOL."\e[93mSet the correct permissions...\e[33m".PHP_EOL;

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

    $archive_path = dirname(PATH_ICMS)."/instantcms_{$core_version['date']}_v{$core_version['version']}-custom.zip";

    exec('cd '.PATH_ICMS.'; /usr/bin/zip -FSr '.$archive_path.' .');

    exit(PHP_EOL.'Archiving is complete. The file is located at '.$archive_path.PHP_EOL);
}

exit(PHP_EOL.'Customization successfully completed.'.PHP_EOL);
exit('You can start InstantCMS installation'.PHP_EOL);
