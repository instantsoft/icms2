<?php

class actionAdminSettingsSchedulerRun extends cmsAction {

    public function run($id=false){

        if (!$id) { cmsCore::error404(); }

        $task = $this->model->getSchedulerTask($id);

        // Проверяем существование контроллера
        if (!cmsCore::isControllerExists($task['controller'])){
            cmsUser::addSessionMessage(sprintf(LANG_CP_SCHEDULER_TASK_RUN_FAIL, $task['title']), 'error');
            $this->redirectBack();
        }

        $controller = cmsCore::getController($task['controller']);

        // Выполняем хук
        $controller->runHook("cron_{$task['hook']}");

        // Обновляем время последнего запуска задачи
        $this->model->updateSchedulerTaskDate($task);

        cmsUser::addSessionMessage(sprintf(LANG_CP_SCHEDULER_TASK_RAN, $task['title'], html_date_time()));

        $this->redirectToAction('settings', array('scheduler'));

    }

}
