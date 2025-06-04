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
     * Значения по умолчанию для формы
     * @var array
     */
    protected $default_item = [];

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
     * @var string|array
     */
    protected $title = '';

    /**
     * Заголовок кнопки сабмита
     * @var ?string
     */
    protected $submit_title = null;

    /**
     * Кнопки тулбара
     * @var array
     */
    protected $tool_buttons = [];

    /**
     * Использовать кнопки по умолчанию: Сохранить/Отменить
     * @var bool
     */
    protected $use_default_tool_buttons = false;

    /**
     * Глубиномер
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * Коллбэки после добавления/обновления записи
     * @var ?callable
     */
    protected $add_callback    = null;
    protected $update_callback = null;

    /**
     * Коллбэк JavaScript, если ответ json
     * @var string
     */
    protected $json_callback = 'reloadDataGrid';

    /**
     * Имя поля с заголовком, к которому добавим (1) при копировании записи
     * @var string
     */
    protected $title_field = 'title';

    public function run($id = null, $is_copy = null) {

        $data = $this->default_item;

        $do = 'add';

        if ($id) {

            if (!$is_copy) {
                $do = 'edit';
            }

            $data = $this->model->localizedOff()->getItemById($this->table_name, $id, function ($item, $model) {
                foreach ($item as $key => $value) {
                    if ($value && strpos($value, '---') === 0) {
                        $item[$key] = cmsModel::yamlToArray($value);
                    }
                    // Если ячейка в БД начинается на date_, то cmsDatabase->prepareValue
                    // null будет CURRENT_TIMESTAMP
                    if (strpos($key, 'date_') === 0 && $value === null) {
                        $item[$key] = 0;
                    }
                }
                return $item;
            });

            if (!$data) {
                return cmsCore::error404();
            }

            if ($is_copy && isset($data[$this->title_field])) {
                $data[$this->title_field] .= ' (1)';
            }

            $this->model->localizedRestore();

            if ($is_copy) {
                $id = null;
            }
        }

        $form_opts = $this->form_opts;
        array_unshift($form_opts, $do);

        $form = $this->getForm($this->form_name, $form_opts);

        if ($this->request->has('csrf_token')) {

            $data = array_merge($data, $form->parse($this->request, true, $data));

            $errors = $form->validate($this, $data);

            if (!$errors) {

                if ($do === 'add') {

                    unset($data['id']);

                    $id = call_user_func_array([$this->model, $this->form_add_method], [$this->table_name, $data]);

                    if ($this->add_callback) {
                        call_user_func_array($this->add_callback, [$id, $data]);
                    }

                } else {

                    call_user_func_array([$this->model, $this->form_edit_method], [$this->table_name, $id, $data]);

                    if ($this->update_callback) {
                        call_user_func_array($this->update_callback, [$data]);
                    }
                }

                if ($this->cache_key) {
                    cmsCache::getInstance()->clean($this->cache_key);
                }

                if ($this->request->isAjax()) {

                    return $this->cms_template->renderJSON([
                        'errors'   => false,
                        'text'     => LANG_SUCCESS_MSG,
                        'callback' => $this->json_callback
                    ]);
                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                if ($this->success_url) {
                    return $this->redirect($this->success_url);
                }

                return $this->redirect($this->getRequestBackUrl($this->getBackURL()));
            }

            if ($errors) {

                if ($this->request->isAjax()) {
                    return $this->cms_template->renderJSON([
                        'errors' => $errors
                    ]);
                }

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $this->cms_template->addToolButtons($this->getToolButtons());

        $html = $this->cms_template->getRenderedAsset('ui/typical_form', [
            'page_title'   => string_replace_keys_values(is_array($this->title) ? ($this->title[$do] ?? '') : $this->title, $data),
            'breadcrumbs'  => $this->breadcrumbs,
            'submit_title' => $this->submit_title,
            'action'       => $this->cms_template->href_to($this->current_action, [$id]),
            'data'         => $data,
            'form'         => $form,
            'errors'       => $errors ?? false
        ]);

        if ($this->request->isStandard()) {
            $this->cms_template->addOutput($html);
        }

        if ($this->request->isAjax()) {
            return $this->cms_core->response->setContent($html)->sendAndExit();
        }

        return $html;
    }

    protected function getToolButtons() {

        $btns = [];

        if ($this->use_default_tool_buttons) {

            $btns[] = [
                'class' => 'save process-save',
                'title' => LANG_SAVE,
                'href'  => '#',
                'icon'  => 'save'
            ];

            if ($this->success_url) {
                $btns[] = [
                    'class' => 'cancel',
                    'title' => LANG_CANCEL,
                    'href'  => $this->success_url,
                    'icon'  => 'undo'
                ];
            }
        }

        if ($this->tool_buttons) {
            foreach ($this->tool_buttons as $button) {
                $btns[] = $button;
            }
        }

        return $btns;
    }

}
