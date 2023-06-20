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

    die('404');
}

// Инициализация
require_once 'bootstrap.php';

// Заходим в рабочую директорию
if (function_exists('chdir')) {
    chdir(PATH);
}

// Локализация по умолчанию
$core->initLanguage();

// Подключаем шаблонизатор, чтобы был подключен хелпер с функциями
cmsTemplate::getInstance();

// Подключение модели
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
    exit;
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

    // если включено последовательное выполнение,
    // параллельные запуски запретить
    if (!empty($task['consistent_run'])) {

        $lock_file = $config->cache_path . 'cron_lock_' . $task['id'];
        $lockfp    = fopen($lock_file, 'w');

        // Если блокировку получить не удалось, значит скрипт еще работает
        // и запуск нужно запретить
        if (!flock($lockfp, LOCK_EX | LOCK_NB)) {
            continue;
        }

        // По окончании работы необходимо снять блокировку и удалить файл
        register_shutdown_function(function ($lockfp, $lock_file) {
            flock($lockfp, LOCK_UN);
            @unlink($lock_file);
        }, $lockfp, $lock_file);
    }

    // Получаем контроллер из коллекции либо загружаем
    // и сохраняем в коллекцию
    if (isset($controllers[$task['controller']])) {

        $controller = $controllers[$task['controller']];

    } else {

        $controller = cmsCore::getController($task['controller']);

        if (!$controller->isEnabled()) {
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

    } catch (Exception $e) {

        // выключаем ошибочное задание
        $model->toggleSchedulerPublication($task['id'], 0);

    }

}
