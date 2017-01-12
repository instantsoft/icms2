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
    $core->route($_SERVER['REQUEST_URI']);

    // Инициализируем шаблонизатор
    $template = cmsTemplate::getInstance();

    if (href_to('auth', 'login') != $_SERVER['REQUEST_URI']){
        if (!$config->is_site_on && !cmsUser::isAdmin()) {
            cmsCore::errorMaintenance();
        }
    }
    // Если гостям запрещено просматривать сайт, перенаправляем на страницу авторизации
    $user = cmsUser::getInstance(); 
    if ($config->is_only_to_users){ 
        if ( (!$user->id) && (!$config->is_site_on && !cmsUser::isAdmin()) ) {
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
