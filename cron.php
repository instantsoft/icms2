<?php

/**
 * @file
 * Файл, который должен вызываться из командной строки, а не по HTTP
 * Некоторые задачи требуют безлимитного времени выполнения, в cli это по умолчанию
 * Задача для CRON выглядит примерно так: /usr/bin/php -f /path_to_site/cron.php
 *
 */
if (PHP_SAPI !== 'cli') {

    http_response_code(404);

    exit('404');
}

// Инициализация
require_once __DIR__ . '/bootstrap.php';

// Заходим в рабочую директорию
if (function_exists('chdir')) {
    chdir(PATH);
}

// Локализация по умолчанию
$core->initLanguage();

// Подключаем шаблонизатор, чтобы был подключен хелпер с функциями
cmsTemplate::getInstance();

/** @var modelAdmin $model */
$model = cmsCore::getModel('admin');

// id задачи
// id передаётся вторым параметром, первым передаётся имя домена
$task_id = isset($argv[2]) ? intval($argv[2]) : 0;

// если id задачи передано, запускаем только её
if ($task_id) {

    $task = $model->getSchedulerTask($task_id);

    if ($task) {
        $tasks = [$task['id'] => $task];
    }

} else {

    // Иначе получаем весь список задач для выполнения
    $tasks = $model->getPendingSchedulerTasks();
}

// Если задач нет, выходим
if (empty($tasks)) {
    exit(0);
}

// Коллекция контроллеров
$controllers = [];

//
// Выполняем задачи по списку
//
foreach ($tasks as $task) {

    // Проверяем существование контроллера
    if (!cmsCore::isControllerExists($task['controller'])) {
        continue;
    }

    // Блокировка для последовательного выполнения
    $lockfp = null;
    $lock_file = null;

    // если включено последовательное выполнение,
    // параллельные запуски запретить
    if (!empty($task['consistent_run'])) {

        $lock_file = $config->cache_path . 'cron_lock_' . $task['id'];
        $lockfp    = fopen($lock_file, 'c');

        if ($lockfp === false) {
            error_log("CRON Error: Could not open lock file: {$lock_file}");
            continue;
        }

        // Пытаемся получить блокировку
        if (!flock($lockfp, LOCK_EX | LOCK_NB)) {
            // Блокировка уже кем-то получена, пропускаем задачу
            fclose($lockfp);
            continue;
        }
    }

    // Получаем контроллер из коллекции либо загружаем
    // и сохраняем в коллекцию
    if (isset($controllers[$task['controller']])) {

        $controller = $controllers[$task['controller']];

    } else {

        $controller = cmsCore::getController($task['controller'], null, false);

        if (!$controller || !$controller->isEnabled()) {
            unset($controller);
            continue;
        }

        $controllers[$task['controller']] = $controller;
    }

    try {

        // Выполняем хук
        $controller->runHook("cron_{$task['hook']}");

        // Обновляем время последнего запуска задачи
        $model->updateSchedulerTaskDate($task);

    } catch (Throwable $e) {

        error_log("Cron error in task #{$task['id']}: " . $e->getMessage());

        // выключаем ошибочное задание
        $model->toggleSchedulerPublication($task['id'], 0);

    } finally {
        if ($lockfp !== null) {
            flock($lockfp, LOCK_UN);
            fclose($lockfp);
            // Файл не удаляем, это опасно при параллельных запусках. Он будет переиспользован.
        }
    }

}

exit(0);
