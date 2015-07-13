<?php

	$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);

    // Инициализация
    require_once "bootstrap.php";

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
            $controllers[$task['controller']] = $controller;

        }

        // Выполняем хук
        $controller->runHook("cron_{$task['hook']}");

        // Обновляем время последнего запуска задачи
        $model->updateSchedulerTaskDate($task['id']);

    }
