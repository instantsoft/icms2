<?php

namespace icms\traits\controllers\actions;

use cmsUser;
use cmsCore;
use cmsForm;
use cmsEventsManager;

/**
 * Трейт для экшена формы
 *
 * @property \cmsTemplate $cms_template
 * @property \cmsRequest $request
 * @property \modelContent $model_content
 *
 */
trait formFieldItem {

    /**
     * Имя формы
     * @required
     * @var string
     */
    protected $form_name = '';

    /**
     * URL, на который будет редирект после сохранения формы
     * @required
     * @var string
     */
    protected $success_url = '';

    /**
     * Имя шаблона для вывода формы
     * @required
     * @var string
     */
    protected $tpl_name = '';

    /**
     * Хуки формы, куда передаётся только объект формы
     * @var array
     */
    protected $form_hooks = [];

    /**
     * Хуки формы, куда передаётся объект формы и тип контента
     * @var array
     */
    protected $form_ctype_hooks = [];

    public function run($ctype_id = null, $field_id = null, $is_copy = null) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        // id типа контента
        if (is_numeric($ctype_id)) {

            $ctype = $this->model_content->getContentType($ctype_id);
            if (!$ctype) {
                return cmsCore::error404();
            }

            $controller_name = 'content';

            $this->dispatchEvent('ctype_loaded', [$ctype, 'field']);

        } else { // Контроллер

            if (!$this->isControllerInstalled($ctype_id)) {
                return cmsCore::error404();
            }

            cmsCore::loadControllerLanguage($ctype_id);

            $ctype = [
                'title' => string_lang($ctype_id . '_controller'),
                'name'  => $ctype_id === 'users' ? '{users}' : $ctype_id, // таблица юзеров
                'id'    => null
            ];

            $this->model_content->setTablePrefix('');

            $controller_name = $ctype_id;
        }

        $do = 'add';

        // Если передан
        if($field_id){

            $field = $this->model_content->localizedOff()->getContentField($ctype['name'], $field_id);
            if (!$field) {
                return cmsCore::error404();
            }

            $this->model_content->localizedRestore();

            if(!$is_copy){

                $do = 'edit';

            } else {

                // Системные не можем копировать
                if ($field['is_system']) {
                    return cmsCore::error404();
                }

                $field['title'] .= ' (copy)';
            }

        } else {

            $field = [
                'ctype_id' => $ctype['id'],
                'is_fixed_type' => null
            ];
        }

        $form = $this->getForm($this->form_name, [$do, $ctype['name']]);

        // Общий хук
        list($form, $ctype, $field) = cmsEventsManager::hook('content_form_field', [$form, $ctype, $field]);

        // Применяем хуки
        if($this->form_hooks){
            $form = cmsEventsManager::hook($this->form_hooks, $form);
        }
        if($this->form_ctype_hooks){

            foreach ($this->form_ctype_hooks as $hook_name) {

                list($form, $ctype) = cmsEventsManager::hook(string_replace_keys_values($hook_name, $ctype), [$form, $ctype]);
            }
        }

        // При редактировании скрываем ненужные поля
        if($do === 'edit'){

            // скроем поле "Системное имя" для фиксированных полей
            if ($field['is_fixed']) {
                $form->hideField('basic', 'name');
            }

            // Скроем для системных и фиксированных полей тип поля
            if ($field['is_system'] || $field['is_fixed_type']) {
                // Для валидации списка меняем на все доступные поля
                $form->setFieldProperty('type', 'type', 'generator', function () use($controller_name) {
                    return cmsForm::getAvailableFormFields(false, $controller_name);
                });
                $form->hideField('type', 'type');
            }

            // скроем лишние опции для системных полей
            if ($field['is_system']) {
                $form->hideField('basic', 'hint');
                $form->hideField('visibility', 'options:relation_id');
                $form->setFieldProperty('visibility', 'options:is_in_item_pos', 'is_visible', false);
                $form->hideFieldset('group');
                $form->hideFieldset('format');
                $form->hideFieldset('values');
                $form->hideFieldset('labels');
                $form->hideFieldset('wrap');
                $form->hideFieldset('edit_access');
            }
        }

        if ($this->request->has('submit')) {

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $field_type   = $this->request->get('type', '');
            $field_class  = 'field' . string_to_camel('_', $field_type);

            if (!class_exists($field_class)) {
                return cmsCore::error(ERR_CLASS_NOT_FOUND);
            }

            $field_object = new $field_class(null, [
                'subject_name' => $ctype['name']
            ]);

            $field_options = $field_object->getOptions();

            $form->addFieldsetAfter('type', LANG_CP_FIELD_TYPE_OPTS, 'field_settings');

            $form->mergeForm($this->makeForm(function ($form) use ($field_options) {

                $form->addFieldset(LANG_CP_FIELD_TYPE_OPTS, 'field_settings');

                foreach ($field_options as $field_field) {

                    $field_field->setName("options:{$field_field->name}");

                    $form->addField('field_settings', $field_field);
                }

                return $form;
            }));

            $defaults = ['ctype_id' => $ctype['id']];
            if($field['is_fixed_type']){
                $defaults['type'] = $field['type'];
            }

            $_field = array_merge($form->parse($this->request, true), $defaults);
            $errors = $form->validate($this, $_field);

            if (!$errors) {

                if($do === 'edit'){

                    $this->model_content->updateContentField($ctype['name'], $field_id, $_field);

                    cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                } else {

                    $field_id = $this->model_content->addContentField($ctype['name'], $_field, $field_object->is_virtual);

                    cmsUser::addSessionMessage(sprintf(LANG_CP_FIELD_CREATED, $_field['title']), 'success');
                }

                $this->redirect(string_replace_keys_values($this->success_url, $ctype));
            }

            if ($errors) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                $field = array_merge($field, $_field);
            }
        }

        return $this->cms_template->render($this->tpl_name, [
            'do'     => $do,
            'ctype'  => $ctype,
            'field'  => $field,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
