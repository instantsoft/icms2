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

    // Получение списка задач для выполнения
    $tasks = $model->getPendingSchedulerTasks();

    // Если задач нет, выходим
    if (!$tasks) { exit; }

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
