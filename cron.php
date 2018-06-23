<?php

    // некоторые задачи требуют безлимитного времени выполнения, в cli это по умолчанию
    // задача для CRON выглядит примерно так: php -f /path_to_site/cron.php
    // Если планируете запускать задачи CRON через curl или иные http запросы, закомментируйте строку ниже
    if(PHP_SAPI != 'cli') { die('Access denied'); }

    // Инициализация
    require_once 'bootstrap.php';

    // Запрещаем дублирующие запуски
    $lock_file = cmsConfig::get('cache_path').'cron_lock';
    $lockfp    = fopen($lock_file, 'w');

    // Если блокировку получить не удалось, значит скрипт еще работает
    // и запуск нужно запретить
    if (!flock($lockfp, LOCK_EX | LOCK_NB)) {
        exit;
    }

    // По окончании работы необходимо снять блокировку и удалить файл
    register_shutdown_function(function() use ($lockfp, $lock_file) {
        flock($lockfp, LOCK_UN);
        @unlink($lock_file);
    });

    // Подключаем шаблонизатор, чтобы был подключен хелпер с функциями
    cmsTemplate::getInstance();

    // Подключение модели
    $model = cmsCore::getModel('admin');

    // id задачи
    // id передаётся вторым параметром, первым передаётся имя домена
    $task_id = isset($argv[2]) ? (int)$argv[2] : 0;

    // если id задачи передано, запускаем только её
    if($task_id){

        $task = $model->getSchedulerTask($task_id);

        if($task){
            $tasks = array($task['id'] => $task);
        }

    } else {

        // Иначе получаем весь список задач для выполнения
        $tasks = $model->getPendingSchedulerTasks();

    }

    // Если задач нет, выходим
    if (empty($tasks)) { exit; }

    // Коллекция контроллеров
    $controllers = array();

    //
    // Выполняем задачи по списку
    //
    foreach($tasks as $task){

        // Проверяем существование контроллера
        if (!cmsCore::isControllerExists($task['controller'])){ continue; }

        // Получаем контроллер из коллекции либо загружаем
        // и сохраняем в коллекцию
        if (isset($controllers[$task['controller']])){

            $controller = $controllers[$task['controller']];

        } else {

            $controller = cmsCore::getController($task['controller']);

            if(!$controller->isEnabled()){
                unset($controller); continue;
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
