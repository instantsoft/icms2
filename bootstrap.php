<?php

    // Определяем корень
    define('PATH', dirname(__FILE__));
	define('ROOT', rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR));

    // Устанавливаем кодировку
    mb_internal_encoding('UTF-8');

	// Подключаем автозагрузчик классов
	require_once PATH . '/system/config/autoload.php';

    // Устанавливаем обработчик автозагрузки классов
    spl_autoload_register('autoLoadCoreClass');

    cmsCore::startTimer();

	// Инициализируем конфиг
	$config = cmsConfig::getInstance();

    // Проверяем, что система установлена
    if (!$config->isReady()){
        $root = str_replace(str_replace(DIRECTORY_SEPARATOR, '/', realpath(ROOT)), '', str_replace(DIRECTORY_SEPARATOR, '/', PATH));
        header('location:'.$root.'/install/');
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
    cmsCore::loadLib('spyc.class');
    // подключаем хелпер шаблона, если он есть
    if(!cmsCore::includeFile('templates/'.$config->template.'/assets/helper.php')){
        cmsCore::loadLib('template.helper');
    }

    // Инициализируем ядро
    $core = cmsCore::getInstance();

    // Подключаем базу
    $core->connectDB();

    if(!$core->db->ready()){
        cmsCore::error(ERR_DATABASE_CONNECT, $core->db->connectError());
    }

    // Запускаем кеш
    cmsCache::getInstance()->start();