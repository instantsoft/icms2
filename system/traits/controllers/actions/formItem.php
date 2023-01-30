<?php

namespace icms\traits\controllers\actions;

use cmsUser;
use cmsCore;
use cmsCache;
use cmsModel;

/**
 * Трейт для экшена формы
 *
 * @property \cmsTemplate $cms_template
 * @property \cmsUser $cms_user
 * @property \cmsRequest $request
 * @property \cmsModel $model
 *
 */
trait formItem {

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
     * Имя формы
     * @required
     * @var string
     */
    protected $form_name = '';

    /**
     * Параметры, передающиеся в форму
     * @var array
     */
    protected $form_opts = [];

    /**
     * URL, на который будет редирект после сохранения формы
     * @var string
     */
    protected $success_url = '';

    /**
     * Метод модели для внесения данных
     * @var string
     */
    protected $form_add_method = 'insert';

    /**
     * Метод модели для обновления данных
     * @var string
     */
    protected $form_edit_method = 'update';

    /**
     * Заголовок страницы
     * @var string
     */
    protected $title = '';

    /**
     * Кнопки тулбара
     * @var array
     */
    protected $tool_buttons = [];

    /**
     * Глубиномер
     * @var array
     */
    protected $breadcrumbs = [];

    public function run($id = null){

        $data = [];

        $do = 'add';

        if($id){

            $data = $this->model->localizedOff()->getItemById($this->table_name, $id, function ($item, $model) {
                foreach ($item as $key => $value) {

                    if ($value && strpos($value, '---') === 0) {
                        $item[$key] = cmsModel::yamlToArray($value);
                    }
                }
                return $item;
            });

            if(!$data){
                return cmsCore::error404();
            }

            $this->model->localizedRestore();

            $do = 'edit';
        }

        $form = $this->getForm($this->form_name, [$do] + $this->form_opts);

        if ($this->request->has('csrf_token')){

            $data = $form->parse($this->request, true, $data);

            $errors = $form->validate($this, $data);

            if (!$errors){

                if($do === 'add'){

                    $id = call_user_func_array([$this->model, $this->form_add_method], [$this->table_name, $data]);

                } else {

                    call_user_func_array([$this->model, $this->form_edit_method], [$this->table_name, $id, $data]);
                }

                if($this->cache_key){

                    cmsCache::getInstance()->clean($this->cache_key);
                }

                if($this->request->isAjax()){

                    return $this->cms_template->renderJSON([
                        'errors'   => false,
                        'text'     => LANG_SUCCESS_MSG,
                        'callback' => 'reloadDataGrid'
                    ]);
                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                if($this->success_url){
                    $this->redirect($this->success_url);
                }

                $this->redirect($this->getRequestBackUrl($this->getBackURL()));
            }

            if ($errors){

                if($this->request->isAjax()){
                    return $this->cms_template->renderJSON([
                        'errors' => $errors
                    ]);
                }

				cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $this->cms_template->addToolButtons($this->tool_buttons);

        $html = $this->cms_template->getRenderedAsset('ui/typical_form', [
            'page_title'  => string_replace_keys_values($this->title, $data),
            'breadcrumbs' => $this->breadcrumbs,
            'action'      => $this->cms_template->href_to($this->current_action, [$id]),
            'data'        => $data,
            'form'        => $form,
            'errors'      => isset($errors) ? $errors : false
        ]);

        if ($this->request->isStandard()) {
            $this->cms_template->addOutput($html);
        }

        if($this->request->isAjax()){
            die($html);
        }

        return $html;
    }

}
