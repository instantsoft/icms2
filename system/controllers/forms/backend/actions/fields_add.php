<?php

class actionFormsFieldsAdd extends cmsAction {

    public function run($form_id, $copy_id = null) {

        $form_data = $this->model->getForm($form_id);
        if (!$form_data) {
            cmsCore::error404();
        }

        $form = $this->getForm('field', ['add', $form_data['id']]);

        $field = ['form_id' => $form_id];

        if ($copy_id) {

            $field = $this->model->localizedOff()->getFormField($copy_id);

            if (!$field) {
                return cmsCore::error404();
            }

            $this->model->localizedRestore();

            $field['title'] .= ' (copy)';

            unset($field['id']);
        }

        list($form, $form_data, $field) = cmsEventsManager::hook('forms_field_form', [$form, $form_data, $field]);

        if ($this->request->has('submit')) {

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $this->addFieldOptionsToForm($form);

            $field = array_merge($field, $form->parse($this->request, true));

            $errors = $form->validate($this, $field);

            if (!$errors) {

                // сохраняем поле
                $field_id = $this->model->addFormField($field);

                if ($field_id) {
                    cmsUser::addSessionMessage(sprintf(LANG_CP_FIELD_CREATED, $field['title']), 'success');
                }

                return $this->redirectToAction('form_fields', [$form_data['id']]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render([
            'menu'      => $this->getFormMenu('edit', $form_data['id']),
            'do'        => 'add',
            'form_data' => $form_data,
            'field'     => $field,
            'form'      => $form,
            'errors'    => isset($errors) ? $errors : false
        ]);
    }

}
