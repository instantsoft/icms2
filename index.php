<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS 2                                   //
//                        http://instantcms.ru/                               //
//                   produced by InstantSoft, instantsoft.ru                  //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    /**
     * Константа, по которой можно отследить текущий тип запуска CMS
     */
    define('VALID_RUN', true);
    /**
     * Константа, наличие которой говорит о том, что нам нужны сессии
     */
    define('SESSION_START', true);

    header('Content-type:text/html; charset=utf-8');

    require_once 'bootstrap.php';

    if ($config->emulate_lag) { usleep(350000); }

    //Запускаем роутинг
    $core->route($_SERVER['REQUEST_URI']);

    // Инициализируем шаблонизатор
    $template = cmsTemplate::getInstance();

    cmsEventsManager::hook('engine_start');

    // загружаем и устанавливаем страницы для текущего URI
    $core->loadMatchedPages();

    // Проверяем доступ
    if(cmsEventsManager::hook('page_is_allowed', true)){

        //Запускаем контроллер
        $core->runController();

    }

    // формируем виджеты
    $core->runWidgets();

    //Выводим готовую страницу
    $template->renderPage();

    cmsEventsManager::hook('engine_stop');

    // Останавливаем кеш
    cmsCache::getInstance()->stop();
