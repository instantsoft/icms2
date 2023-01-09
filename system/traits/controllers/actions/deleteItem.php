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

    public function run($id) {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $item = $this->model->getItemById($this->table_name, $id);

        if (!$item) {
            return cmsCore::error404();
        }

        $success = true;

        if($this->delete_callback){

            $success = call_user_func_array($this->delete_callback, [$item, $this->model]);
        }

        if($success){

            $this->model->delete($this->table_name, $id);

            if($this->cache_key){

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
