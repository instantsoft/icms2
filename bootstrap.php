<?php

/**
 * @file
 * Файл первоначальной инициализации окружения InstantCMS
 *
 */

// Определяем корень
define('PATH', dirname(__FILE__));

// оставлено для совместимости, если кто-то использовал эту константу
// в CMS не используется нигде
define('ROOT', rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR));

// Устанавливаем кодировку
mb_internal_encoding('UTF-8');

// Подключаем автозагрузчик пакетов Composer
if (is_readable(PATH . '/vendor/autoload.php')) {
    require_once PATH . '/vendor/autoload.php';
}

// Подключаем автозагрузчик классов CMS
require_once PATH . '/system/config/autoload.php';

// Устанавливаем обработчик автозагрузки классов
spl_autoload_register('autoLoadCoreClass');

// Инициализируем конфиг
$config = cmsConfig::getInstance();

// Проверяем, что система установлена
if (!$config->isReady()) {

    // Отправляем на установку
    if (PHP_SAPI !== 'cli') {

        $root = str_replace(str_replace(DIRECTORY_SEPARATOR, '/', realpath($_SERVER['DOCUMENT_ROOT'])), '', str_replace(DIRECTORY_SEPARATOR, '/', PATH));
        header('location:' . $root . '/install/');
        die;
    }

    die('no config');
}

// включаем отладку CMS
if ($config->debug) {

    cmsDebugging::enable();
}

// Стартуем сессию если константа SESSION_START объявлена
if (defined('SESSION_START')) {

    cmsUser::sessionStart($config);
}

// Устанавливаем часовую зону
// Могла быть изменена в cmsUser::sessionStart
date_default_timezone_set($config->time_zone);

// Подключаем все необходимые классы и библиотеки
cmsCore::loadLib('html.helper');
cmsCore::loadLib('strings.helper');
cmsCore::loadLib('files.helper');
if (!$config->native_yaml) {
    cmsCore::loadLib('spyc.class');
}

// Инициализируем ядро
$core = cmsCore::getInstance();

// Подключаем базу
$core->connectDB();

// соединение не установлено? Показываем ошибку
if (!$core->db->ready()) {

    cmsCore::loadLanguage();

    return cmsCore::error($core->db->connectError());
}

// Запускаем кеш
cmsCache::getInstance()->start();

// Регистрируем остановку кэша
register_shutdown_function(function () {

    cmsCache::getInstance()->stop();
});

cmsEventsManager::hook('core_start');
