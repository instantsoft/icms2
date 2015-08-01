<?php

    // Определяем корень
    define('PATH', __DIR__);
	define('ROOT', $_SERVER['DOCUMENT_ROOT']);

    // Устанавливаем кодировку
    mb_internal_encoding('UTF-8');

    // Подключаем автозагрузчик классов и пакетов composer
    if (file_exists(PATH . '/vendor/autoload.php')) {
        require_once PATH . '/vendor/autoload.php';
    }

	// Подключаем автозагрузчик классов
	require_once PATH . '/system/config/autoload.php';

    // Устанавливаем обработчик автозагрузки классов
    spl_autoload_register('autoLoadCoreClass');

	// Инициализируем конфиг
	$config = cmsConfig::getInstance();

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
