<?php

    // Определяем корень
    define('PATH', dirname(__FILE__));
	define('ROOT', $_SERVER['DOCUMENT_ROOT']);

    // Устанавливаем кодировку
    mb_internal_encoding('UTF-8');

	// Подключаем автозагрузчик классов
	require_once PATH . '/system/config/autoload.php';

    // Устанавливаем обработчик автозагрузки классов
    spl_autoload_register('autoLoadCoreClass');

	// Инициализируем конфиг
	$config = cmsConfig::getInstance();

    // Проверяем, что система установлена
    if (!$config->isReady()){
        header('location:'.str_replace(rtrim(ROOT, DIRECTORY_SEPARATOR), '', PATH).'/install/');
        die();
    }

    // Загружаем локализацию
    cmsCore::loadLanguage();

    // Устанавливаем часовую зону
    date_default_timezone_set( $config->time_zone );

    // Подключаем все необходимые классы и библиотеки
	cmsCore::loadLib('html.helper');
	cmsCore::loadLib('strings.helper');
	cmsCore::loadLib('files.helper');

    // Инициализируем ядро
    $core = cmsCore::getInstance();

    // Подключаем базу
    $core->connectDB();

    if(!$core->db->ready()){
        cmsCore::error(ERR_DATABASE_CONNECT, $core->db->connectError());
    }

    // Запускаем кеш
    cmsCache::getInstance()->start();