<?php

    // Определяем корень
    define('PATH', dirname(__FILE__));

    // оставлено для совместимости, если кто-то использовал эту константу
    // в CMS не используется нигде
    define('ROOT', rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR));

    // Устанавливаем кодировку
    mb_internal_encoding('UTF-8');

    // Подключаем автозагрузчик классов
    require_once PATH . '/system/config/autoload.php';

    // Устанавливаем обработчик автозагрузки классов
    spl_autoload_register('autoLoadCoreClass');

    // Инициализируем конфиг
    $config = cmsConfig::getInstance();

    // дебаг отключен - скрываем все сообщения об ошибках
    if(!$config->debug){

        error_reporting(0);

    } else {

        error_reporting(E_ALL);

        // включаем отладку
        cmsDebugging::enable();

    }

    // Проверяем, что система установлена
    if (!$config->isReady()){
        $root = str_replace(str_replace(DIRECTORY_SEPARATOR, '/', realpath($_SERVER['DOCUMENT_ROOT'])), '', str_replace(DIRECTORY_SEPARATOR, '/', PATH));
        header('location:'.$root.'/install/');
        die();
    }

    // Устанавливаем часовую зону
    date_default_timezone_set( $config->time_zone );

    // Подключаем все необходимые классы и библиотеки
    cmsCore::loadLib('html.helper');
    cmsCore::loadLib('strings.helper');
    cmsCore::loadLib('files.helper');
    cmsCore::loadLib('spyc.class');

    // Инициализируем ядро
    $core = cmsCore::getInstance();

    // Подключаем базу
    $core->connectDB();

    if(!$core->db->ready()){
        cmsCore::error($core->db->connectError());
    }

    // Запускаем кеш
    cmsCache::getInstance()->start();

    cmsEventsManager::hook('core_start');

    // Загружаем локализацию
    cmsCore::loadLanguage();

    // устанавливаем локаль языка
    if(function_exists('lang_setlocale')){
        lang_setlocale();
    }

    // устанавливаем локаль MySQL
    $core->db->setLcMessages();
