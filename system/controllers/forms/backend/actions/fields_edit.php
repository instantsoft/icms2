<?php

class actionFormsFieldsEdit extends cmsAction {

    public function run($field_id){

        $field = $this->model->getFormField($field_id);
        if(!$field){ cmsCore::error404(); }

        $form_data = $this->model->getForm($field['form_id']);
        if(!$form_data){ cmsCore::error404(); }

        $form = $this->getForm('field', array('edit', $form_data['id']));

        list($form, $form_data, $field) = cmsEventsManager::hook('forms_field_form', [$form, $form_data, $field]);

        if ($this->request->has('submit')){

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $field_type    = $this->request->get('type', '');
            $field_class   = 'field' . string_to_camel('_', $field_type);
            $field_object  = new $field_class(null, null);
            $field_options = $field_object->getOptions();
            $form->addFieldsetAfter('type', LANG_CP_FIELD_TYPE_OPTS, 'field_settings');
            foreach ($field_options as $option_field) {
                $option_field->setName("options:{$option_field->name}");
                $form->addField('field_settings', $option_field);
            }

            $field = array_merge($field, $form->parse($this->request, true));
            $errors = $form->validate($this, $field);

            if (!$errors){

                // если не выбрана группа, обнуляем поле группы
                if (!$field['fieldset']) { $field['fieldset'] = null; }

                // если создается новая группа, то выбираем ее
                if ($field['new_fieldset']) { $field['fieldset'] = $field['new_fieldset']; }
                unset($field['new_fieldset']);

                // сохраняем поле
                $this->model->updateFormField($field_id, $field);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('form_fields', array($form_data['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('backend/fields_add', array(
            'menu'   => $this->getFormMenu('edit', $form_data['id']),
            'form_data' => $form_data,
            'do'     => 'edit',
            'field'  => $field,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
