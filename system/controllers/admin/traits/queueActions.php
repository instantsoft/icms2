<?php

namespace icms\controllers\admin\traits;

use cmsCore, cmsQueue, cmsForm;

/**
 * Трейт для экшенов очередей
 */
trait queueActions {

    /**
     * Должно быть объявлено свойство
    public $queue = [];
     *
     */

    /**
     * Добавляем пункт меню
     * @return array
     */
    public function getBackendMenu() {

        $this->backend_menu[] = [
            'title' => sprintf(LANG_CP_QUEUE_TITLE, $this->queue['queue_name']),
            'url'   => href_to($this->root_url, 'queue'),
            'options' => [
                'icon' => 'recycle'
            ]
        ];

        return $this->backend_menu;
    }

    /**
     * Экшен очереди
     *
     * @return string
     */
    public function actionQueue() {

        if (empty($this->queue['use_queue_action'])) {
            return cmsCore::error404();
        }

        $grid = $this->controller_admin->loadDataGrid('queue', ['contex_controller' => $this]);

        if ($this->request->isAjax()) {

            $filter     = [];
            $filter_str = $this->request->get('filter', '');

            if ($filter_str) {
                parse_str($filter_str, $filter);
            }

            $this->controller_admin->model->filterIn('queue', $this->queue['queues']);

            $total = $this->controller_admin->model->getCount(cmsQueue::getTableName());

            $perpage = isset($filter['perpage']) ? $filter['perpage'] : 30;
            $page    = isset($filter['page']) ? intval($filter['page']) : 1;

            $this->controller_admin->model->limitPage($page, $perpage);

            $this->controller_admin->model->orderByList([
                ['by' => 'date_started', 'to' => 'asc'],
                ['by' => 'priority', 'to' => 'desc'],
                ['by' => 'date_created', 'to' => 'asc']
            ]);

            $jobs = $this->controller_admin->model->get(cmsQueue::getTableName());

            return $this->cms_template->renderGridRowsJSON($grid, $jobs, $total);
        }

        return $this->cms_template->getRenderedAsset('ui/grid', [
            'grid'       => $grid,
            'page_title' => sprintf(LANG_CP_QUEUE_TITLE, $this->queue['queue_name']),
            'source_url' => href_to($this->root_url, 'queue'),
        ]);
    }

    /**
     * Экшен рестарта задания очереди
     *
     * @param integer $job_id
     * @return type
     */
    public function actionQueueRestart($job_id) {

        if (empty($this->queue['use_queue_action'])) {
            return cmsCore::error404();
        }

        cmsQueue::restartJob(['id' => $job_id]);

        return $this->redirectBack();
    }

    /**
     * Экшен удаления задания очереди
     *
     * @param integer $job_id
     * @return type
     */
    public function actionQueueDelete($job_id) {

        if (empty($this->queue['use_queue_action'])) {
            return cmsCore::error404();
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        cmsQueue::deleteJob(['id' => $job_id]);

        return $this->redirectBack();
    }

}
