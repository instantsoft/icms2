<?php

/**
 * @file
 * Файл, который обслуживает все запросы страниц InstantCMS.
 *
 * Весь код InstantCMS выпущен в соответствии с лицензией GNU General Public License v2.
 * Смотрите файлы license.en.txt и license.ru.txt в корне вашей установки копии InstantCMS.
 * Сделано в InstantSoft, instantsoft.ru, instantcms.ru.
 */

/**
 * Константа, по которой можно отследить текущий тип запуска CMS
 */
define('VALID_RUN', true);

/**
 * Константа, наличие которой говорит о том, что нам нужны сессии
 * и bootstrap.php их включит
 */
define('SESSION_START', true);

header('Content-type:text/html; charset=utf-8');
header('X-Powered-By: InstantCMS');

// Подключаем файл первоначальной инициализации окружения InstantCMS
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
