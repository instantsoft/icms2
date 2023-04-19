<?php

class actionFormsFieldsEdit extends cmsAction {

    public function run($field_id) {

        $field = $this->model->localizedOff()->getFormField($field_id);

        if (!$field) {
            return cmsCore::error404();
        }

        $this->model->localizedRestore();

        $form_data = $this->model->getForm($field['form_id']);

        if (!$form_data) {
            return cmsCore::error404();
        }

        $form = $this->getForm('field', ['edit', $form_data['id']]);

        list($form, $form_data, $field) = cmsEventsManager::hook('forms_field_form', [$form, $form_data, $field]);

        if ($this->request->has('submit')) {

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $this->addFieldOptionsToForm($form);

            $field  = array_merge($field, $form->parse($this->request, true));
            $errors = $form->validate($this, $field);

            if (!$errors) {

                // сохраняем поле
                $this->model->updateFormField($field_id, $field);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                return $this->redirectToAction('form_fields', [$form_data['id']]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/fields_add', [
            'menu'      => $this->getFormMenu('edit', $form_data['id']),
            'form_data' => $form_data,
            'do'        => 'edit',
            'field'     => $field,
            'form'      => $form,
            'errors'    => isset($errors) ? $errors : false
        ]);
    }

}
