<?php

/**
 * @file
 * Файл, который обслуживает все HTTP запросы страниц InstantCMS.
 *
 * Весь код InstantCMS выпущен в соответствии с лицензией GNU General Public License v2.
 * Смотрите файлы license.en.txt и license.ru.txt в корне вашей установки копии InstantCMS.
 * Сделано в InstantSoft, instantsoft.ru, instantcms.ru.
 */

/**
 * Константа, по наличию которой можно отследить текущий тип запуска CMS
 * Её значение - текущий таймстамп, используется для отладки
 */
define('VALID_RUN', microtime(true));

/**
 * Константа, наличие которой говорит о том, что нам нужны сессии
 * и bootstrap.php их включит
 */
define('SESSION_START', true);

// Подключаем файл первоначальной инициализации окружения InstantCMS
require_once 'bootstrap.php';

// Запускаем
$core->runHttp($_SERVER['REQUEST_URI']);
