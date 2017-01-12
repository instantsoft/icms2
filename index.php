<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS 2                                   //
//                        http://instantcms.ru/                               //
//                   produced by InstantSoft, instantsoft.ru                  //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/
	session_start();

	define('VALID_RUN', true);

	// Устанавливаем кодировку
	header('Content-type:text/html; charset=utf-8');
    header('X-Powered-By: InstantCMS 2');

    require_once 'bootstrap.php';

    if ($config->emulate_lag) { usleep(350000); }

    //Запускаем роутинг
    $core->route(href_to_current());

    // Инициализируем шаблонизатор
    $template = cmsTemplate::getInstance();

    // Если сайт выключен, закрываем его от посетителей
    if (href_to('auth', 'login') != href_to_current()){
        if (!$config->is_site_on && !cmsUser::isAdmin()) {
            cmsCore::errorMaintenance();
        }
    }

    // Если гостям запрещено просматривать сайт, перенаправляем на страницу авторизации
    if (strpos(href_to_current(), href_to('auth')) !== 0) {
        if ($config->is_only_to_users && !cmsUser::isLogged()) { 
            cmsUser::goLogin(); 
        }
    }
	
    cmsEventsManager::hook('engine_start');

    //Запускаем контроллер
	$core->runController();
    $core->runWidgets();

    //Выводим готовую страницу
    $template->renderPage();

    cmsEventsManager::hook('engine_stop');

    // Останавливаем кеш
    cmsCache::getInstance()->stop();
