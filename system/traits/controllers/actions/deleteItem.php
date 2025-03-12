<?php

namespace icms\traits\controllers\actions;

use cmsUser;
use cmsCore;
use cmsForm;
use cmsCache;

/**
 * Трейт для экшена удаления записи из таблицы
 *
 * @property \cmsRequest $request
 * @property \cmsModel $model
 *
 */
trait deleteItem {

    /**
     * Основная таблица БД
     * @required
     * @var string
     */
    protected $table_name = '';

    /**
     * Ключ кэша
     * @var string
     */
    protected $cache_key = '';

    /**
     * URL, на который будет редирект после удаления
     * @var string
     */
    protected $success_url = '';

    /**
     * Коллбэк до удаления записи из таблицы. Если вернёт false, запись не будет удалена
     * @var callable
     */
    protected $delete_callback = null;

    /**
     * Имя request переменной со списком id
     * @var string
     */
    protected $ids_key = 'selected';

    public function run($id = null) {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        if ($id) {
            $ids = [$id];
        } else {
            $ids = $this->request->get($this->ids_key, []);
        }

        if (!$ids) {
            return cmsCore::error404();
        }

        $items = $this->model->filterIn('id', $ids)->get($this->table_name);

        if (!$items) {
            return cmsCore::error404();
        }

        $success = true;

        foreach ($items as $item) {

            if (!$success) {
                continue;
            }

            if ($this->delete_callback) {
                $success = call_user_func_array($this->delete_callback, [$item, $this->model]);
            }

            if ($success) {
                $this->model->delete($this->table_name, $item['id']);
            }
        }

        if ($success) {

            if ($this->cache_key) {
                cmsCache::getInstance()->clean($this->cache_key);
            }

            cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');
        }

        if ($this->success_url) {
            return $this->redirect($this->success_url);
        }

        return $this->redirect($this->getRequestBackUrl($this->getBackURL()));
    }

}
